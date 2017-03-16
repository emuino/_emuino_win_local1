<?php

$cfgFile = CFG_FILE_INIT;
$initList = isset ($_POST["initList"]) ? $_POST["initList"] : array ();

foreach ($initList as $key => $value) {
	$initList[$key]=trim ($initList[$key]);
}

$errText = "";
$successText = "";
$infoText = "# Default Host localhost, HTTP-Port 80, HTTPS 443\r\n";
switch ($view) {

	case "init":
		if ($cmd=="save") {
			$host = getArrayValue ($initList, "httpHost");
			if (empty ($host))
			{
				$errText = "You have to define a Host or IP-Address where the HTTP-Server will run.";
			}
			else
			{
				$port = (int) getArrayValue ($initList, "httpPort");
				if (empty ($port) || $port<0 || $port>65535)
					$initList["httpPort"]=80;

				$port = (int) getArrayValue ($initList, "httpsPort");
				if (empty ($port) || $port<0 || $port>65535)
					$initList["httpsPort"]=443;


				$initList["noHTTPS"]=getArrayValue ($initList, "noHTTPS", "false");
			
				$fh = fopen (CFG_PATH.'/'.$cfgFile, "w+");
				fwrite ($fh, $infoText."\r\n");
				
				foreach ($initList as $key => $value) {
					if (empty ($key) && empty ($value))
						continue;
					
					fwrite ($fh, $key."=".$value."\r\n");
	
	
				}
				fclose ($fh);		
				$plus = 0;
				$successText = $cfgFile." was saved.";
			}
		}
		break;
}


if (empty ($errText)) {
	$initList=readKeyValueFile (CFG_PATH.'/'.$cfgFile);
}

$html = '
	<h2>Main configuration ('.$cfgFile.')</h2>';

if (!empty ($errText)) {

	$html.='<p class="error">'.htmlspecialchars ($errText).'</p>';
}
else
if (!empty ($successText)) {

	$html.='<p class="success">'.htmlspecialchars ($successText).'</p>';
}
	
$html.='
	<p><a class="help_link" href="'.$_SERVER["REQUEST_URI"].'" onclick="return showHelp(this);"><i class="fa fa-question-circle"></i> Get Help</a></p><p style="display:none;" class="file_comments">'.nl2br (htmlspecialchars ($infoText)).'</p>
	<form id="form" atcion="'.$_SERVER["REQUEST_URI"].'" method="post">
	<input id="cmd" type="hidden" name="cmd" value="save" />
	<input type="hidden" name="view" value="init" />
	
	<div class="input_fields">	
		<div class="input_row"><label>Host / IP </label><input class="input_long" type="text" name="initList[httpHost]" value="'.htmlspecialchars (getArrayValue ($initList, "httpHost", "localhost")).'" /></div>
		<div class="input_row"><label>HTTP-Port</label><input class="input_small" type="text" name="initList[httpPort]" value="'.htmlspecialchars (getArrayValue ($initList, "httpPort", "80")).'" /></div>
		<div class="input_row"><label>HTTPS-Port</label><input class="input_small" type="text" name="initList[httpsPort]" value="'.htmlspecialchars (getArrayValue ($initList, "httpsPort", "443")).'" /></div>
		<div class="input_row"><label for="no_https">No HTTPS</label><input id="no_https" class="input_small" type="checkbox" name="initList[noHTTPS]" '.(getArrayValue ($initList, "noHTTPS", "false")=="true" ? "checked" : "").' value="true" /></div>
		
		<div class="input_row"><label>Doc path</label><input class="input_long" type="text" name="initList[docPath]" value="'.htmlspecialchars (getArrayValue ($initList, "docPath", "")).'" /></div>
	</div>
	';

$html.='
	<input type="submit" value="Save" />
	</form>
';

?>