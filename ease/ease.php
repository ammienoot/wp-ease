<?php
/*
Plugin Name: EASE Authentication
Version: 2.0
Description: Authenticate users using EASE
Author: Stephen P Vickers / Mark Findlay
*/

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'options-page.php');

class EASEAuthenticationPlugin {

  function EASEAuthenticationPlugin() {

    register_activation_hook(__FILE__, array($this, 'initialize_options'));

    $options_page = new EASEAuthenticationOptionsPage($this, 'ease_authentication_options', __FILE__);

    add_filter('authenticate', 'wp_authenticate_ease', 50, 3);
    add_filter('login_url', array($this, 'bypass_reauth'));
    //add_filter('show_password_fields', array(&$this, 'disable'));
    add_filter('allow_password_reset', array($this, 'disable'));
    add_action('check_passwords', array($this, 'generate_password'), 10, 3);

  }


  /*************************************************************
   * Plugin hooks
   *************************************************************/

  /*
   * Add the default options to the database.
   */
  function initialize_options() {

    $options = array(
      'ease_authenticate_site_on' => 'off',
      'secret' => '',
      'use_remote_user' => true,
      'auto_create_user' => false,
      'ldap_server' => 'ldaps://authorise.is.ed.ac.uk',
      'ldap_base' => 'ou=people,ou=central,dc=authorise,dc=ed,dc=ac,dc=uk'
    );

    update_option('ease_authentication_options', $options);

  }

  /*
   * Remove the reauth=1 parameter from the login URL, if applicable. This allows
   * us to transparently bypass the mucking about with cookies that happens in
   * wp-login.php immediately after wp_signon when a user e.g. navigates directly
   * to wp-admin.
   */
  function bypass_reauth($login_url) {

    $login_url = remove_query_arg('reauth', $login_url);

    return $login_url;

  }

  /*
   * Used to disable certain display elements, e.g. password
   * fields on profile screen, or functions, e.g. password reset.
   */
  function disable($flag) {

    return false;

  }

  /*
   * Generate a password for the user. This plugin does not require the
   * administrator to enter this value, but we need to set it so that user
   * creation and editing works.
   */
  function generate_password($username, $password1, $password2) {

    $password1 = $password2 = wp_generate_password();

  }


  /*************************************************************
   * Functions
   *************************************************************/

  /*
   * Get the value of the specified plugin-specific option.
   */
  function get_plugin_option($option) {

    $options = get_option('ease_authentication_options');

    return $options[$option];

  }

  /*
   * Check the credentials passed on the query string
   */
  function check_remote_user() {

    $secret = $this->get_plugin_option('secret');
    if ((bool)$this->get_plugin_option('use_remote_user') && !empty($_SERVER['REMOTE_USER'])) {
      $username = $_SERVER['REMOTE_USER'];
    } else if (empty($secret) || empty($_GET['userid']) || empty($_GET['timestamp']) || empty($_GET['mac'])) {
      return new WP_Error('empty_username', 'Please log in using the form below.');
    } else {
      $username = trim($_GET['userid']);
      $timestamp = trim($_GET['timestamp']);
      $mac = trim($_GET['mac']);

#  Current time
      $now = time();

###  Check time (+/- 1 minute)
      $ok = ( abs(intval($timestamp)-$now) < 60 );
###  Check MAC
      if ( $ok ) {
        $MAC = ease_authentication_getMAC($username . $timestamp, $secret);
        $ok = ( strcasecmp($MAC, $mac) == 0 );
      }

      if ( !$ok ) {
        wp_redirect('wp-login.php');
        exit();
      }
    }

    // Create new users automatically, if configured
    $user = get_userdatabylogin($username);
    if (! $user) {
      if ((bool) $this->get_plugin_option('auto_create_user')) {
        $user = $this->_create_user($username);
      }
      else {
        return new WP_Error('invalid_username', "User $username does not exist in the WordPress database");
      }
    }

    return $user;

  }

  /*
   * Create a new WordPress account for the specified username.
   */
  function _create_user($username) {

    $user = new WP_Error('', '');
    include_once(WPINC . DIRECTORY_SEPARATOR . 'registration.php');

    $ldapServer = $this->get_plugin_option('ldap_server');
    $ldapBase = $this->get_plugin_option('ldap_base');

    if ($ds = @ldap_connect($ldapServer)) {

      $filter = "uid=$username";
      $attributes = array('mail', 'givenname', 'sn', 'cn');

      $searchResult = @ldap_search($ds, $ldapBase, $filter, $attributes);
      if ($searchResult) {

        $information = @ldap_get_entries($ds, $searchResult);

        if ($information && ($information['count'] == 1)) {

          if ($information[0]['mail']['count'] == 1) {
            $user_email = $information[0]['mail'][0];
          }
          if ($information[0]['givenname']['count'] == 1) {
            $first_name = $information[0]['givenname'][0];
          }
          if ($information[0]['sn']['count'] == 1) {
            $last_name = $information[0]['sn'][0];
          }
          if ($information[0]['cn']['count'] == 1) {
            $display_name = $information[0]['cn'][0];
          }
          if ($user_email && $first_name && $last_name) {

            if (!$display_name) {
              $display_name = "$first_name $last_name";
            }

            $user_login = $username;
            $user_pass = wp_generate_password();

            $userdata = compact('user_login', 'user_pass', 'first_name', 'last_name', 'user_email', 'display_name');

            $user_id = wp_insert_user($userdata);
            $user = get_user_by('id', $user_id);

          }

        }

      }

    }

    return $user;

  }

}

### Calculate a MAC

function ease_authentication_getMAC($data, $secret) {

  $asciivalue = 0;
  $size = strlen($data);
  for ($i=0; $i<$size; $i++) {
    $asciivalue += ord(substr($data, $i, 1));
  }
  $mac = md5($asciivalue . $secret);

  return $mac;

}

// Load the plugin hooks, etc.
$ease_authentication_plugin = new EASEAuthenticationPlugin();

function wp_authenticate_ease($user, $username, $password) {

  global $ease_authentication_plugin;

  if (is_a($user, 'WP_User')) {
    return $user;
  }

  $user = $ease_authentication_plugin->check_remote_user();
  if (! is_wp_error($user)) {
    $user = new WP_User($user->ID);
  }

  return $user;

}

//Display EASE login button if not logged in, and Log-out button if logged in.
function EASE_widget() 
{	
	$options_all = get_option('ease_authentication_options');
	$full_EASE = $options_all['ease_authenticate_site_on'];
	$domain = get_option('siteurl');
	
	if ($full_EASE == 'on')
	{
		if (!is_user_logged_in())
			{ 
				//do_action( 'bp_before_sidebar_login_form' )
				
?>
<script language="JavaScript" type="text/javascript"> 
var t = setTimeout("document.autoEASE.submit();",0);
</script>
<p id="login-text">                        
<form name="autoEASE" action="<?php echo  $domain.'/wp-content/plugins/ease/login' ?>" method="get" style="margin: 0px; padding: 0px;">
	<input type="hidden" name="Login with EASE" id="login" alt="Login with EASE" title="Login to Wordpress with your EASE username and password"/>
</form>

</p>		
		
<?php
			}
	}
	if (!is_user_logged_in() && $full_EASE != 'on')
	{ 
		//do_action( 'bp_before_sidebar_login_form' )
?>	
<p id="login-text">                        
<form action="<?php echo  $domain.'/wp-content/plugins/ease/login' ?>" method="get" style="margin: 0px; padding: 0px;">
	<input type="image" name="Login with EASE" id="login" alt="Login with EASE" src="<?php echo  $domain.'/wp-content/plugins/ease/loginButton.png' ?>" title="Login to Wordpress with your EASE username and password"  style="margin-top:.5em;"/>
</form>
</p>		
		
<?php
	}
	else
	{
?>
<a class="button logout" href="<?php echo wp_logout_url( $domain.'/wp-content/plugins/ease/logout.pl' ) ?>"><?php _e( 'Log Out', 'buddypress' ) ?></a>
<?php	
	}
}


function widget_displayEASE($args)
	{
  		extract($args);
  		echo $before_widget;
		if (!is_user_logged_in())
  			{
 				echo $before_title;?>EASE Login<?php echo $after_title;
  			}
 		else
  			{
	  			echo $before_title;?>EASE Logout<?php echo $after_title;
	  		}

  		EASE_widget();
  		echo $after_widget;
}


 
function EASE_init()
{
  register_sidebar_widget(__('EASE_widget'), 'widget_displayEASE');
}
add_action("plugins_loaded", "EASE_init");

?>