<?php

$cfgFile = CFG_FILE_VHOST;
$vhostList = isset ($_POST["vhostList"]) ? $_POST["vhostList"] : array ();

$errText = "";
$successText = "";
$infoText = "# Definitions of virtual hosts\r\n# [<unique name>=]<hostname1>[,<hostname2> ...];<Directory>\r\n# You can create for every virtual host a own configuration directory: cfg/vhost/<hostname>/\r\n# Following configuration files can be overwritten: mime.txt, vdir.txt, cgi.txt, php.ini (only for FastCGI)\r\n";

if ($cmd=="del") {
	$idx = (int) getParam ("idx", -1);
	$errText = "Virtual host entry deleted.";
	unset ($vhostList[$idx]);		

}

if ($cmd=="save" || $cmd=="del") {
	$fh = fopen (CFG_PATH.'/'.$cfgFile, "w+");
	fwrite ($fh, $infoText."\r\n");
	foreach ($vhostList as $idx => $data) {
	
		foreach ($data as $key => $value) {
			$data[$key]=trim ($value);
		}
		if (
			($data["hostname"]=="" && $data["dir"]==""))
			continue;
	
		fwrite ($fh, $data["hostname"].";".$data["dir"]."\r\n");
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
	$vhostList= array ();
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
    $data["hostname"]=$list[0];
    $data["dir"]=$list[1];
		foreach ($data as $key => $value) {
				$data[$key]=trim ($value);
		} 
   	$vhostList[$idx++]=$data;
	}
	fclose ($fh);		
}


$html = '
	<h2>Virtual hosts ('.$cfgFile.')</h2>';

if (!empty ($errText)) {

	$html.='<p class="error">'.htmlspecialchars ($errText).'</p>';
}
else
if (!empty ($successText)) {

	$html.='<p class="success">'.htmlspecialchars ($successText).'</p>';
}

function showInputLine ($idx, $data) {

	$del = '<a onclick="if (confirm (\'Do you want to delete the virtual host entry?\')) { $(\'#cmd\').val(\'del\');$(\'#idx\').val('.$idx.');$(\'#form\').submit (); }return false;" href="'.$_SERVER["REQUEST_URI"].'">(Del)</a>';
	if (isset($data["not_saved"]))
		$del = "";
	
	return 
		'<tr class="input_row">
			<td><input class="input_wide"  type="text" name="vhostList['.$idx.'][hostname]" value="'.htmlspecialchars ($data["hostname"]).'" /></td>
			<td style="white-space:nowrap;"><input class="input_wide" type="text" name="vhostList['.$idx.'][dir]" value="'.htmlspecialchars ($data["dir"]).'" /> '.$del.'</td>
		</tr>
	';
	
}
	
$htmlRows = "";
foreach ($vhostList as $idx => $data) {

	$htmlRows.=showInputLine ($idx, $data);
	
}

if ($cmd=="plus") {
	for ($i=0;$i<$plus;$i++) {
		$idx++;
		$data = array (
			"hostname" => "",
			"dir" => "",
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
		'.(count ($vhostList)>0 || $plus >0 ? '
		<tr>
			<td>Hostnames (Comma separated)</td>
			<td  style="width:50%">Directory</td>
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