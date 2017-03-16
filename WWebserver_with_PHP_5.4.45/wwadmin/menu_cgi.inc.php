<?php

$cfgFile = CFG_FILE_CGI;
$cgiList = isset ($_POST["cgiList"]) ? $_POST["cgiList"] : array ();

$errText = "";
$successText = "";
$infoText = "# Definitions of CGI programs\r\n# .<Ext 1> .<Ext 2> ...;<Full path to CGI program>\r\n# .<Ext 1> .<Ext 2> ...;PHPBuildIn\r\n# .<Ext 1> .<Ext 2> ...;PHPFCGI [<Full path to php-cgi.exe>]\r\n# .<Ext 1> .<Ext 2> ...;FCGI <Full path to FastCGI program> | <IP>:<Port> or <Pipename>\r\n# .<Ext 1> .<Ext 2> ...;FCGI <IP>:<Port> or <Pipename>\r\n";

if ($cmd=="del") {
	$idx = (int) getParam ("idx", -1);
	$errText = "CGI definition entry deleted.";
	unset ($cgiList[$idx]);		

}

if ($cmd=="save" || $cmd=="del") {
	$fh = fopen (CFG_PATH.'/'.$cfgFile, "w+");
	fwrite ($fh, $infoText."\r\n");
	foreach ($cgiList as $idx => $data) {
	
		foreach ($data as $key => $value) {
			$data[$key]=trim ($value);
		}
		if (
			($data["alias"]=="" && $data["program"]==""))
				continue;
	
		fwrite ($fh, $data["alias"].";".$data["program"]."\r\n");
	}
	fclose ($fh);		
	$plus = 0;
	if (empty ($errText))
		$successText = $cfgFile." was saved.";

}

$htmlInfoText = "";

// Load 
$fh = fopen (CFG_PATH.'/'.$cfgFile, "r");		
if ($fh) {
	$cgiList= array ();
	$idx = 0;	
	while (!feof($fh)) {				
    $line = trim (fgets($fh), "\r\n ");
    if (empty ($line))
			continue;
    if (startsWith ($line, "#")) {
    	$htmlInfoText.=htmlspecialchars ($line).'<br />';
			continue;
    }
    $list=multiexplode (array (";"), $line);
    if (count ($list)<2)
    	continue;
    	
    $data = array ();
    $data["alias"]=$list[0];
    $data["program"]=$list[1];
		foreach ($data as $key => $value) {
				$data[$key]=trim ($value);
		} 
   	$cgiList[$idx++]=$data;
	}
	fclose ($fh);		
}


$html = '
	<h2>CGI/FASTCGI definitions ('.$cfgFile.')</h2>';

if (!empty ($errText)) {

	$html.='<p class="error">'.htmlspecialchars ($errText).'</p>';
}
else
if (!empty ($successText)) {

	$html.='<p class="success">'.htmlspecialchars ($successText).'</p>';
}

function showInputLine ($idx, $data) {

	$del = '<a onclick="if (confirm (\'Do you want to delete the CGI definition entry?\')) { $(\'#cmd\').val(\'del\');$(\'#idx\').val('.$idx.');$(\'#form\').submit (); }return false;" href="'.$_SERVER["REQUEST_URI"].'">(Del)</a>';
	if (isset($data["not_saved"]))
		$del = "";
	
	return 
		'<tr class="input_row">
			<td><input class="input_wide"  type="text" name="cgiList['.$idx.'][alias]" value="'.htmlspecialchars ($data["alias"]).'" /></td>
			<td style="white-space:nowrap;"><input class="input_wide" type="text" name="cgiList['.$idx.'][program]" value="'.htmlspecialchars ($data["program"]).'" /> '.$del.'</td>
		</tr>
	';
	
}
	
$htmlRows = "";
foreach ($cgiList as $idx => $data) {

	$htmlRows.=showInputLine ($idx, $data);
	
}

if ($cmd=="plus") {
	for ($i=0;$i<$plus;$i++) {
		$idx++;
		$data = array (
			"alias" => "",
			"program" => "",
			"not_saved" => 1,
		
		);
		$htmlRows.=showInputLine ($idx, $data);
	
	}
}	

$html.='
	<p><a class="help_link" href="'.$_SERVER["REQUEST_URI"].'" onclick="return showHelp(this);"><i class="fa fa-question-circle"></i> Get Help</a></p><p style="display:none;" class="file_comments">'.nl2br (htmlspecialchars ($infoText)).'</p>
	<form id="form" atcion="'.$_SERVER["REQUEST_URI"].'" method="post">
	<input id="cmd" type="hidden" name="cmd" value="save" />
	<input id="idx" type="hidden" name="idx" value="-1" />
	<input id="plus" type="hidden" name="plus" value="'.($cmd=="plus" ? ($plus+1) : 1).'" />
	<input type="hidden" name="view" value="crontab" />
	<div class="input_fields">
	
	<table>
		'.(count ($cgiList)>0 || $plus >0 ? '
		<tr>
			<td>File extensions (.ext1 .ext2 ...)</td>
			<td  style="width:50%">CGI Program</td>
		</tr>
		'
		:
		'').'	
	'.$htmlRows;


$html.='
	</table>';
$html.='<div class="input_row"><a href="'.$_SERVER["REQUEST_URI"].'" onclick="$(\'#cmd\').val(\'plus\');$(\'#form\').submit ();return false;">+ Append</a></div>';

$html.='
	</div>
	<input type="submit" value="Save" />
	</form>
';
?>