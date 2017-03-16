<?php

$cfgFile = CFG_FILE_VDIR;
$vdirList = isset ($_POST["vdirList"]) ? $_POST["vdirList"] : array ();

$errText = "";
$successText = "";
$infoText = "# Definitions of virtual directories\r\n# <Path>;<Directory>\r\n# Note: There must be an '/' on end of <Path>\r\n";

if ($cmd=="del") {
	$idx = (int) getParam ("idx", -1);
	$errText = "Virtual directory entry deleted.";
	unset ($vdirList[$idx]);		

}

if ($cmd=="save" || $cmd=="del") {
	$fh = fopen (CFG_PATH.'/'.$cfgFile, "w+");
	fwrite ($fh, $infoText."\r\n");
	foreach ($vdirList as $idx => $data) {
	
		foreach ($data as $key => $value) {
			$data[$key]=trim ($value);
		}
		if (
			($data["vdir"]=="" && $data["dir"]==""))
			continue;
	
		$data["vdir"] = rtrim ($data["vdir"], "/");	
		fwrite ($fh, $data["vdir"]."/;".$data["dir"]."\r\n");
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
	$vdirList= array ();
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
    $data["vdir"]=$list[0];
    $data["dir"]=$list[1];
		foreach ($data as $key => $value) {
				$data[$key]=trim ($value);
		} 
   	$vdirList[$idx++]=$data;
	}
	fclose ($fh);		
}


$html = '
	<h2>Virtual directories ('.$cfgFile.')</h2>';

if (!empty ($errText)) {

	$html.='<p class="error">'.htmlspecialchars ($errText).'</p>';
}
else
if (!empty ($successText)) {

	$html.='<p class="success">'.htmlspecialchars ($successText).'</p>';
}

function showInputLine ($idx, $data) {

	$del = '<a onclick="if (confirm (\'Do you want to delete the virtual directory entry?\')) { $(\'#cmd\').val(\'del\');$(\'#idx\').val('.$idx.');$(\'#form\').submit (); }return false;" href="'.$_SERVER["REQUEST_URI"].'">(Del)</a>';
	if (isset($data["not_saved"]))
		$del = "";
	
	return 
		'<tr class="input_row">
			<td><input class="input_wide"  type="text" name="vdirList['.$idx.'][vdir]" value="'.htmlspecialchars ($data["vdir"]).'" /></td>
			<td style="white-space:nowrap;"><input class="input_wide" type="text" name="vdirList['.$idx.'][dir]" value="'.htmlspecialchars ($data["dir"]).'" /> '.$del.'</td>
		</tr>
	';
	
}
	
$htmlRows = "";
foreach ($vdirList as $idx => $data) {

	$htmlRows.=showInputLine ($idx, $data);
	
}

if ($cmd=="plus") {
	for ($i=0;$i<$plus;$i++) {
		$idx++;
		$data = array (
			"vdir" => "",
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
		'.(count ($vdirList)>0 || $plus >0 ? '
		<tr>
			<td>Path</td>
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