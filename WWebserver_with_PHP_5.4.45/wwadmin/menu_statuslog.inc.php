<?php

$cfgFile = "../log/status.log";

$errText = "";
$successText = "";

switch ($cmd) {

	case "delete":
		if (is_file ($cfgFile))
			unlink ($cfgFile);
		break;
}


$html.='<h2>Status Log (last 100 lines)</h2>';
$html.='<p><button onclick="if (confirm (\'Do you want to delete status log file?\')) window.location.href=\'?view=statuslog&cmd=delete\';">Delete log</button></p>';
$html.='<div class="log_file">';
if (is_file ($cfgFile)) {
	$logList = readLastLinesOfFile ($cfgFile, 100);
	foreach ($logList as $log) {
		$html.=htmlspecialchars ($log)."<br />";
	}
}
$html.='</div>';

?>