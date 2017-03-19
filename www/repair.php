<?php

if(!isset($_GET['sketch']) || !$_GET['sketch'] || !isset($_GET['device']) || !$_GET['device']) {
	die('ERROR: skatch or device is not defined');
}
if(preg_match('/(\/|\\)/', $_GET['sketch'])) {
	die('ERROR: subfolder detected in filename');
}
if(!preg_match('/.ino$/', $_GET['sketch'])) {
	die('ERROR: only .ino extension acceptable');
}

$cfile = '../emuino/emuino.cpp';

$c = file_get_contents($cfile);

$sketch = $_GET['sketch'];
$device = $_GET['device'];

$c = preg_replace('/^\#define\s+SKETCH\s+\"sketch\/([^\"]+)\"/m', '#define SKETCH "sketch/'.$sketch.'"', $c);
$c = preg_replace('/^\#define\s+([^\s]+)\s*\/\/\s*__DEVICE_TYPE__/m', '#define '.$device.' //__DEVICE_TYPE__', $c);

if(!file_put_contents($cfile, $c)) {
	die('ERROR: file write error: '.$cfile);
}

die('OK');