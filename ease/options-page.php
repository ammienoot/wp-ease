<?php
class EASEAuthenticationOptionsPage {

  var $plugin;
  var $group;
  var $page;
  var $title;

  function EASEAuthenticationOptionsPage($plugin, $group, $page, $title = 'EASE authentication') {

    $this->plugin = $plugin;
    $this->group = $group;
    $this->page = $page;
    $this->title = $title;

    add_action('admin_init', array($this, 'register_options'));
    add_action('admin_menu', array($this, 'add_options_page'));

  }

  /*
   * Register the options for this plugin so they can be displayed and updated below.
   */
  function register_options() {

    register_setting($this->group, $this->group);

    $section = 'ease_authentication_main';
    add_settings_section($section, 'Main Options', array($this, '_display_options_section'), $this->page);
    add_settings_field('ease_authenticate_site_on', 'Level of authentication?', array($this, '_display_option_ease_authenticate_site'), $this->page, $section);
    add_settings_field('ease_authentication_secret', 'Shared secret', array($this, '_display_option_secret'), $this->page, $section);
    //add_settings_field('ease_authentication_use_remote_user', 'Use REMOTE_USER?', array($this, '_display_option_use_remote_user'), $this->page, $section);
    add_settings_field('ease_authentication_auto_create_user', 'Automatically create user accounts?', array($this, '_display_option_auto_create_user'), $this->page, $section);
    add_settings_field('ease_authentication_show_toolbar_create_user', 'Show Toolbar for created user accounts?', array($this, '_display_option_show_toolbar_create_user'), $this->page, $section);
    add_settings_field('ease_authentication_ldap_server', 'URL of LDAP server', array($this, '_display_option_ldap_server'), $this->page, $section);
    add_settings_field('ease_authentication_ldap_base', 'Base DN for the LDAP directory', array($this, '_display_option_ldap_base'), $this->page, $section);
  }

  /*
   * Add an options page for this plugin.
   */
  function add_options_page() {

    if (function_exists('is_site_admin') && is_site_admin()) {
      add_submenu_page('wpmu-admin.php', $this->title, $this->title, 'manage_options', $this->page, array($this, '_display_options_page'));
      add_options_page($this->title, $this->title, 'manage_options', $this->page, array($this, '_display_options_page'));
    }
    else {
      add_options_page($this->title, $this->title, 'manage_options', $this->page, array($this, '_display_options_page'));
    }

  }

  /*
   * Display the options for this plugin.
   */
  function _display_options_page() {
    $bulk_create_url = plugins_url('login/create.php', __FILE__);
    
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2>EASE authentication options</h2>
  <form action="options.php" method="post">
    <?php settings_fields($this->group); ?>
    <?php do_settings_sections($this->page); ?>
    <p class="submit">
      <input type="submit" name="Submit" value="<?php esc_attr_e('Save changes'); ?>" class="button-primary" />
    </p>
  </form>
  <p>
    <strong>Bulk create accounts:</strong> Create accounts from a list of UUNs on the
    <a href="<?php echo($bulk_create_url); ?>">bulk create accounts</a> page.
  </p>
</div>
<?php

  }

  /*
   * Display explanatory text for the main options section.
   */
  function _display_options_section() {
  }
  
    /*
   * Display the shared EASE authenticate site/posts field.
   */
  function _display_option_ease_authenticate_site() {

    $ease_authenticate_site_on = $this->plugin->get_plugin_option('ease_authenticate_site_on');
?>
<p>EASE authenticate full site:&nbsp;&nbsp;&nbsp; 
<input name="<?php echo htmlspecialchars($this->group); ?>[ease_authenticate_site_on]" id="ease_authentication_site_on" value="on" type="checkbox" <?php if ($ease_authenticate_site_on) echo ' checked="checked"'?>/><br />
Select this option to require EASE authentication for the whole site. If this setting is left blank anyone will be able to view content, but will need to EASE authenticate to post to the site.
</p>
<?php

  }

  /*
   * Display the shared secret field.
   */
  function _display_option_secret() {

    $secret = $this->plugin->get_plugin_option('secret');
?>
<input type="text" name="<?php echo htmlspecialchars($this->group); ?>[secret]" id="ease_authentication_secret" value="<?php echo htmlspecialchars($secret) ?>" size="50" /><br />
Shared secret used to secure login requests.
<?php

  }
  
  /*
   * Display the use REMOTE_USER field.
   */
  function _display_option_use_remote_user() {

    $use_remote_user = $this->plugin->get_plugin_option('use_remote_user');
?>
<input type="checkbox" name="<?php echo htmlspecialchars($this->group); ?>[use_remote_user]" id="ease_authentication_use_remote_user"<?php if ($use_remote_user) echo ' checked="checked"' ?> value="1" /><br />
Use REMOTE_USER environment variable to identify user if set?<br />
This variable is automatically set by EASE if the folder is protected by cosign.
<?php

  }

  /*
   * Display the automatically create accounts checkbox.
   */
  function _display_option_auto_create_user() {

    $auto_create_user = $this->plugin->get_plugin_option('auto_create_user');
?>
<input type="checkbox" name="<?php echo htmlspecialchars($this->group); ?>[auto_create_user]" id="ease_authentication_auto_create_user"<?php if ($auto_create_user) echo ' checked="checked"' ?> value="1" /><br />
Should a new user be created automatically if not already in the WordPress database?<br />
Created users will given the default role as defined in the system options.
<?php

  }

  /*
   * Display the show toolbar when creating accounts checkbox.
   */
  function _display_option_show_toolbar_create_user() {

    $show_toolbar_create_user = $this->plugin->get_plugin_option('show_toolbar_create_user');
?>
<input type="checkbox" name="<?php echo htmlspecialchars($this->group); ?>[show_toolbar_create_user]" id="ease_authentication_show_toolbar_create_user"<?php if ($show_toolbar_create_user) echo ' checked="checked"' ?> value="1" /><br />
When a new user is created automatically, do you want them to see the WordPress Toolbar at the top of each page?
<?php

  }

  /*
   * Display the LDAP server field.
   */
  function _display_option_ldap_server() {

    $ldap_server = $this->plugin->get_plugin_option('ldap_server');
?>
<input type="text" name="<?php echo htmlspecialchars($this->group); ?>[ldap_server]" id="ease_authentication_ldap_server" value="<?php echo htmlspecialchars($ldap_server) ?>" size="50" /><br />
URL of LDAP server from which to fetch name and email address of new users.
<?php

  }

  /*
   * Display the LDAP base field.
   */
  function _display_option_ldap_base() {

    $ldap_base = $this->plugin->get_plugin_option('ldap_base');
?>
<input type="text" name="<?php echo htmlspecialchars($this->group); ?>[ldap_base]" id="ease_authentication_ldap_base" value="<?php echo htmlspecialchars($ldap_base) ?>" size="75" /><br />
Base DN for the LDAP directory.
<?php

  }

}
?>
