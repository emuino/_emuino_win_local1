<?php

$cfgFile = CFG_FILE_PHP_INI;

$errText = "";
$successText = "";
$infoText = "#\r\n";
switch ($view) {

	case "php":
		if ($cmd=="save") {
			$fh = fopen (CFG_PATH."/".$cfgFile, "w+");
			$content = getParam ("file_content");
			fwrite ($fh, $content);
			fclose ($fh);
			$successText = $cfgFile." was saved.";			
		}
		break;
}



$html = '
	<h2>PHP configuration ('.$cfgFile.')</h2>';

if (!empty ($errText)) {

	$html.='<p class="error">'.htmlspecialchars ($errText).'</p>';
}
else
if (!empty ($successText)) {

	$html.='<p class="success">'.htmlspecialchars ($successText).'</p>';
}

$content = "";
if (is_file (CFG_PATH."/".$cfgFile))
	$content = file_get_contents (CFG_PATH."/".$cfgFile);
	
$html.='

	<form id="form" atcion="'.$_SERVER["REQUEST_URI"].'" method="post">
	<input id="cmd" type="hidden" name="cmd" value="save" />
	<input type="hidden" name="view" value="php" />
	
	<div class="input_fields">	
		<textarea  spellcheck="false" name="file_content" style="width:100%;min-height:600px;">'.htmlspecialchars ($content).'</textarea>
	</div>
	';

$html.='
	<input type="submit" value="Save" />
	</form>
';

?>