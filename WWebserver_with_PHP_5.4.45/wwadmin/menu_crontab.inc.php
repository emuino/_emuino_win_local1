<?php

$cfgFile = CFG_FILE_CRONTAB;
$crontabList = isset ($_POST["crontabList"]) ? $_POST["crontabList"] : array ();

$errText = "";
$successText = "";
$infoText = "# <min> <hour> <day> <month> <weekday> <url>|exec <cmd> [<resultfile>]\r\n# Weekday: 1 = Mon, 0 = Sun\r\n# */<value> = Interval";

if ($cmd=="del") {
	$idx = (int) getParam ("idx", -1);
	$errText = "Crontab entry deleted.";
	unset ($crontabList[$idx]);		

}

if ($cmd=="save" || $cmd=="del") {
	$fh = fopen (CFG_PATH.'/'.$cfgFile, "w+");
	fwrite ($fh, $infoText."\r\n");
	foreach ($crontabList as $idx => $data) {
	
		foreach ($data as $key => $value) {
			$data[$key]=trim ($value);
		}
		if (
			($data["min"]=="*" || $data["min"]=="") && 
			($data["hour"]=="*" || $data["hour"]=="") && 
			($data["day"]=="*" || $data["day"]=="") && 
			($data["month"]=="*" || $data["month"]=="") && 
			($data["weekday"]=="*" || $data["weekday"]=="") && 						
			$data["cmd"]=="")
			continue;
	
		fwrite ($fh, $data["min"]."\t".$data["hour"]."\t".$data["day"]."\t".$data["month"]."\t".$data["weekday"]."\t".$data["cmd"]."\r\n");
	}
	fclose ($fh);		
	$plus = 0;
	if (empty ($errText))
		$successText = $cfgFile." was saved.";

}

$htmlInfoText = "";

// Load crontab
$fh = fopen (CFG_PATH.'/'.$cfgFile, "r");		
if ($fh) {
	$crontabList = array ();
	$idx = 0;	
	while (!feof($fh)) {				
    $line = trim (fgets($fh), "\r\n ");
    if (empty ($line))
			continue;
    if (startsWith ($line, "#")) {
    	$htmlInfoText.=htmlspecialchars ($line).'<br />';
			continue;
    }
    $list=multiexplode (array (" ","\t"), $line);
    if (count ($list)<5)
    	continue;
    	
    $data = array ();
    $data["min"]=$list[0];
    $data["hour"]=$list[1];
    $data["day"]=$list[2];
    $data["month"]=$list[3];
    $data["weekday"]=$list[4];
    $data["cmd"]=$list[5];
    for ($i=6;$i<count ($list);$i++) {
			$data["cmd"].=(" ".$list[$i]);
		}   
		foreach ($data as $key => $value) {
				$data[$key]=trim ($value);
		} 
   	$crontabList[$idx++]=$data;
	}
	fclose ($fh);		
}


$html = '
	<h2>Cronjobs ('.$cfgFile.')</h2>';

if (!empty ($errText)) {

	$html.='<p class="error">'.htmlspecialchars ($errText).'</p>';
}
else
if (!empty ($successText)) {

	$html.='<p class="success">'.htmlspecialchars ($successText).'</p>';
}

function showCrontabLine ($idx, $data) {

	$del = '<a onclick="if (confirm (\'Do you want to delete the crontab entry?\')) { $(\'#cmd\').val(\'del\');$(\'#idx\').val('.$idx.');$(\'#form\').submit (); }return false;" href="'.$_SERVER["REQUEST_URI"].'">(Del)</a>';
	if (isset($data["not_saved"]))
		$del = "";
	
	return 
		'<tr class="input_row">
			<td><input type="text" name="crontabList['.$idx.'][min]" value="'.htmlspecialchars ($data["min"]).'" /></td>
			<td><input  type="text" name="crontabList['.$idx.'][hour]" value="'.htmlspecialchars ($data["hour"]).'" /></td>
			<td><input  type="text" name="crontabList['.$idx.'][day]" value="'.htmlspecialchars ($data["day"]).'" /></td>
			<td><input  type="text" name="crontabList['.$idx.'][month]" value="'.htmlspecialchars ($data["month"]).'" /></td>
			<td><input  type="text" name="crontabList['.$idx.'][weekday]" value="'.htmlspecialchars ($data["weekday"]).'" /></td>
			<td style="white-space:nowrap;"><input class="input_wide"  type="text" name="crontabList['.$idx.'][cmd]" value="'.htmlspecialchars ($data["cmd"]).'" /> '.$del.'</td>
		</tr>
	';
	
}
	
$htmlRows = "";
foreach ($crontabList as $idx => $data) {

	$htmlRows.=showCrontabLine ($idx, $data);
	
}

if ($cmd=="plus") {
	for ($i=0;$i<$plus;$i++) {
		$idx++;
		$data = array (
			"min" => "*",
			"hour" => "*",
			"day" => "*",
			"month" => "*",
			"weekday" => "*",
			"cmd" => "",
			"not_saved" => 1,
		
		);
		$htmlRows.=showCrontabLine ($idx, $data);
	
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
		'.(count ($crontabList)>0 || $plus >0 ? '
		<tr>
			<td>Minute</td>
			<td>Hour</td>
			<td>Day</td>
			<td>Month</td>
			<td>Weekday</td>
			<td style="width:60%">URL / Cmd</td>
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