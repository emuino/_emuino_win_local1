<?php
session_start ();
define ('CFG_PATH', '../cfg');
define ('CFG_FILE_INIT', 'init.txt');
define ('CFG_FILE_VDIR', 'vdir.txt');
define ('CFG_FILE_CGI', 'cgi.txt');
define ('CFG_FILE_VHOST', 'vhost.txt');
define ('CFG_FILE_PHP_INI', 'php.ini');
define ('CFG_FILE_CRONTAB', 'crontab.txt');

require_once ("utils.inc.php");
require_once ("class.wwadminsesison.inc.php");

$cmd  = getParam ("cmd");
$view = getParam ("view", "status");
$plus  = (int) getParam ("plus", 0);
$html = "";
$htmlLogout = "";
$htmlMenu = "";

$session = new WWAdminSession ();
if ($view=="logout")
	$session->logout ();
if ($view=="request_pwd" || !is_file ("cfg/login.txt")) {
	include ("request_pwd.inc.php");
}
else
if (!$session->isLogin ()) {
	include ("login.inc.php");
}
else {
	$htmlLogout = '<a class="logout" title="Logout" href="?view=logout"><i class="fa fa-power-off"></i></a>';	
	if (is_file ("menu_".$view.".inc.php"))
		include ("menu_".$view.".inc.php");

	$menuList = array (
		"status" => "Overview/Status",
		"init" => "Basic configuration",
		"vdir" => "Virtual directories",
		"cgi" => "CGI/FCGI configuration",
		"php" => "PHP configuration",
		"vhost" => "Virtual hosts",
		"crontab" => "Cronjobs",
		"statuslog" => "Status Log",
		"errorlog" => "Error Log",
		"change_pwd" => "Change password",
	
	);
	
	foreach ($menuList as $menuView => $name) {
		$htmlMenu.='<div class="menu_item"><a '.($menuView==$view ? ' class="marked" ' : '').' href="?view='.$menuView.'">'.htmlspecialchars ($name).'</a></div>';
	}
	
	$htmlMenu = '<div class="menu">'.$htmlMenu.'</div>';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>WWebserver Admin</title>
<meta name="language" content="en" />
<meta name="robots" content="nondex,nfollow" />
<meta name="viewport" content="width=device-width, initial-scale=1,  minimum-scale=1, maximum-scale=1" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="shortcut icon" href="images/favicon.ico" /> 
<!--[if lte IE 8]>
<style type="text/css">@import url(css/style_ie8.css);</style>
<![endif]-->
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/utils.js"></script>
</head>
<body id="top">
<div class="header_frame">
		<?php echo $htmlLogout; ?>
		<a href="<?php echo $_SERVER["PHP_SELF"];?>"><img class="logo" src="images/ww_logo.png"  alt="WWebserver" /></a><h1><a href="<?php echo $_SERVER["PHP_SELF"];?>">WWebserver Administration Panel</a></h1>
</div>
<div class="row">
	

	<?php echo $htmlMenu; ?>
	
	<div class="content_frame">
	
	
		<?php echo $html; ?>
		<!-- <i class="fa fa-envelope"></i>&nbsp;&nbsp;mwiede@mwiede.de</a> -->
		<div style="clear:both;"></div>
	
	</div>
</div>
<div id="popupWindowFilter" class="filter"></div>
<a id="page_nav_up" class="fa fa-angle-up slider_link" href="#top"></a>
</body>
</html>