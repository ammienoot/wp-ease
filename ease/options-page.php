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
  
}
?>
