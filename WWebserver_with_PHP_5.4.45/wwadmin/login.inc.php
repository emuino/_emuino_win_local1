<?php

$errText = "";
$successText = "";

if ($cmd=="login") {
	$password = getParam ("password");

	if (empty ($password))
	{
		$errText = "Please insert password.";
	}
	else
	{
		if ($session->login ($password)) {
			$successText = "Login successful.";
			header ("Location: index.php?view=status");
			exit;
		}
		else {
			$errText = "Password invalid.";
		}
	}
	
}




$html = '
	<div class="login_frame">

	<h2>Login</h2>';

if (!empty ($errText)) {

	$html.='<p class="error">'.htmlspecialchars ($errText).'</p>';
}
else
if (!empty ($successText)) {

	$html.='<p class="success">'.htmlspecialchars ($successText).'</p>';
}
	
$html.='
	
	<form id="form" atcion="index.php" method="post">
	<input id="cmd" type="hidden" name="cmd" value="login" />
	
	<div class="input_fields">	
		<div class="input_row"><label>Password</label><input class="input_long" type="password" name="password" value="" /></div>
	</div>
	';

$html.='
	<p><input type="submit" value="Login" /></p>
	<a href="?view=request_pwd">Password forgotten?</a></p>
	</form>
	</div>

';

?>