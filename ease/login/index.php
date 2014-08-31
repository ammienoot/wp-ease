<?php

require_once( '../../../../wp-config.php' );

$options = get_option('ease_authentication_options');
$secret = $options['secret'];

$redirect_to = site_url() . DIRECTORY_SEPARATOR;
$url = "{$redirect_to}wp-login.php";

$redirect_to = urlencode($redirect_to);

$username = $_SERVER['REMOTE_USER'];
$now = time();
$mac = ease_authentication_getMAC($username . $now, $secret);

header("Location: $url?userid=$username&timestamp=$now&mac=$mac&redirect_to=$redirect_to");

?>
