<?php

$errText = "";
$successText = "";



$html = '
	<div class="login_frame">

	<h2>Set new password</h2>';

if (!empty ($errText)) {

	$html.='<p class="error">'.htmlspecialchars ($errText).'</p>';
}
else
if (!empty ($successText)) {

	$html.='<p class="success">'.htmlspecialchars ($successText).'</p>';
}
	
$html.='
	<p>Execute <span class="log_file">C:\WWebserver\reset_pwd.bat</span> to create a new password for WWadmin.</p>
	
	';

$html.='

	<a href="?view=login">&lt; Back to login mask</a></p>
	</form>
	</div>

';

?>