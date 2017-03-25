<?php
if(!isset($_GET['fname']) || !$_GET['fname']) {
	die('filename is not set');
}
if(!file_exists('../'.$_GET['fname'])) {
	die('file not found: '.$_GET['fname']);
}
$batchfile = file_get_contents('../'.$_GET['fname']);
$size = strlen($batchfile);
header('Content-Disposition: attachment; filename="'.$_GET['fname'].'"');
header('Content-Type: BAT MIME TYPE or something like application/octet-stream');
header('Content-Lenght: '.$size);
echo $batchfile;
