<?php


$errText = "";
$successText = "";
$infoText = "\r\n";
switch ($view) {

	case "change_pwd":
		if ($cmd=="save") {
			$oldPassword = trim (getParam ("old_password"));
			$newPassword = trim (getParam ("new_password"));
			$reply= trim (getParam ("reply"));
			if (empty ($newPassword) || strlen ($newPassword)<5)
			{
				$errText = "New password need min. 5 characters.";
			}
			else
			if (strcmp ($newPassword, $reply)!=0) 
			{
				$errText = "Passwords not equal.";				
			}
			else
			if (!$session->changePassword ($oldPassword, $newPassword)) {
				$errText = "Cannot change password. Old password wrong.";						
			}
			else {				
				$successText = "Password was changed.";
			}
		}
		break;
}


$html = '
	<h2>Change password</h2>';

if (!empty ($errText)) {

	$html.='<p class="error">'.htmlspecialchars ($errText).'</p>';
}
else
if (!empty ($successText)) {

	$html.='<p class="success">'.htmlspecialchars ($successText).'</p>';
}
	
$html.='
	<form id="form" atcion="'.$_SERVER["REQUEST_URI"].'" method="post">
	<input id="cmd" type="hidden" name="cmd" value="save" />
	<input type="hidden" name="view" value="change_pwd" />
	
	<div class="input_fields">	
		<div class="input_row"><label>Old password</label><input class="input_long" type="password" name="old_password" value="" /></div>
		<div class="input_row"><label>New password</label><input class="input_long" type="password" name="new_password" value="" /></div>
		<div class="input_row"><label>Reply</label><input class="input_long" type="password" name="reply" value="" /></div>
	</div>
	';

$html.='
	<input type="submit" value="Change" />
	</form>
';

?>