<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head>
<title>ltiapps blogs &rsaquo; Log In</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name='robots' content='noindex,nofollow' />
</head>
<body>
<h1>Create WordPress user accounts</h1>

<p>
<?php

require_once( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'wp-config.php' );

$list = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $uuns = preg_split("/[\s,]+/", $_POST['uuns']);
  foreach ($uuns as $uun) {
    echo "&nbsp;&nbsp;&nbsp;--&gt;&nbsp;create account for $uun...&nbsp;";

    $user = get_user_by( 'login', $uun );
    if (! $user) {
      $user = $ease_authentication_plugin->_create_user($uun);
      if ($user && ! is_wp_error($user)) {
        echo 'OK';
      } else {
        $list .= "$uun\n";
        echo $user->get_error_message();
      }
    } else {
      echo 'Already exists';
    }
    echo "<br />\n";
  }
}

?>

<form action="" method="POST">
<p>
  Enter a list of UUNs in the box below, separated by commas, spaces, or on separate lines.
</p>
<p>
UUNS:<br />
<textarea name="uuns" rows="10" cols="50"><?php echo $list; ?></textarea>
</p>
<p>
<input type="submit" value="Create accounts" />
</p>
</form>
</body>
</html>
