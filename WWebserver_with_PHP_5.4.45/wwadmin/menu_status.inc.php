<?php

$cfgFile = "../log/current_status.log";

$errText = "";
$successText = "";

switch ($cmd) {

	case "restart":
		$fh = fopen ("../restart", "w+");
		fclose ($fh);
		echo "OK";
		exit;
		break;
}

$html.='<h2>WWebserver Status</h2>';




// Load 
$statusKeyList = array ();
if (is_file ($cfgFile)) {
	$fh = fopen ($cfgFile, "r");		
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
	    $list=multiexplode (array ("="), $line);
	    if (count ($list)<2)
	    	continue;
	    $key = trim ($list[0]);
	   	$statusKeyList[$key]=trim ($list[1]);
		}
		fclose ($fh);		
	}
}
else {
	$errText = "Status file log/current_status.log not found. WWebserver seems not running.";
}

if (!empty ($errText)) {

	$html.='<p class="error">'.htmlspecialchars ($errText).'</p>';
}
else
if (!empty ($successText)) {

	$html.='<p class="success">'.htmlspecialchars ($successText).'</p>';
}



// Status table
$keys = array (

	"server_software" => "Server software",
	"http_running" => "HTTP running",
	"http_host" => "HTTP host",
	"https_running" => "HTTPS running",
	"https_host" => "HTTPS host",
	"status_date" => "Last status date",
	"running_since" => "Running since",
	"process_id" => "Process ID",
	"process_memory" => "Process memory",
	"threads_total" => "Threads",
	"threads_working" => "Threads busy",
	"fcgi_processes" => "FCGI processes",
	
	);
$htmlStatusTable = "";

foreach ($keys as $key => $name) {

	$value = getArrayValue ($statusKeyList, $key);
	if ($key=="process_memory")
		$value = formatKBText ($value);
	$htmlStatusTable.='
		<tr>
			<td>'.htmlspecialchars ($name).'</td>
			<td>'.htmlspecialchars ($value).'</td>
		</tr>
	';
}
	
$html.='
<table class="status_tbl">
'.$htmlStatusTable.'
</table>
';

// Restart
$initList = readKeyValueFile (CFG_PATH."/".CFG_FILE_INIT);

if (getArrayValue ($initList, "noHTTPS")!="true" && isset ($_SERVER["HTTPS"]) && !empty ($_SERVER["HTTPS"])) {
	$protocol = "https";
	$urlPort = ":".$initList["httpsPort"];
	if (isset ($initList["httpsHost"]))
		$host = $initList["httpsHost"];
	if (empty ($host))
		$host = $initList["httpHost"];
}
else {
	$protocol = "http";
	$urlPort = ":".$initList["httpPort"];
	$host = $initList["httpHost"];

}
$restartURL = $protocol."://".$host.$urlPort."/wwadmin/";


$html.='
	<form action="'.$_SERVER["REQUEST_URI"].'" method="post">
	
		<input type="hidden" name="cmd" value="restart" />
		<div class="input_fields">
			<input type="submit" value="Restart Webserver" onclick="if (!confirm (\'Do you want to restart Webserver on '.$initList["httpHost"].':'.$initList["httpPort"].'?\')) { return false; } else { restartWebserver (\''.$restartURL.'\'); return false };" />
		</div>
	</form>
	';

// Logs
$cfgFile = "../log/status.log";
$html.='<p><b>Last status log:</b></p>';
$html.='<p class="log_file">';
if (is_file ($cfgFile)) {
	$logList = readLastLinesOfFile ($cfgFile, 10);
	foreach ($logList as $log) {
		$html.=htmlspecialchars ($log)."<br />";
	}
}
$html.='</p>';

$cfgFile = "../log/error.log";
$html.='<p><b>Last error log:</b></p>';
$html.='<p class="log_file">';
if (is_file ($cfgFile)) {
	$logList = readLastLinesOfFile ($cfgFile, 10);
	foreach ($logList as $log) {
		$html.=htmlspecialchars ($log)."<br />";
	}
}
$html.='</p>';

?>