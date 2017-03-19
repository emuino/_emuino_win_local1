<?php


if($f = isset($_GET['f']) ? $_GET['f'] : '') {
	
	if(!file_exists($f)) {
		die("file not found: $f");
	}
	
	if(pathinfo($f, PATHINFO_EXTENSION) != 'bat') {
		die("only .bat files available");
	}
	
	if(pathinfo(realpath($f), PATHINFO_DIRNAME) != pathinfo(realpath(__FILE__), PATHINFO_DIRNAME)) {
		die("requested folder path disabled");
	}
	
	$min = true; //isset($_GET['min']) && $_GET['min'];
			
	$f = "start " . ($min ? "/min \"\" " : "") . "$f"; 
	
	echo exec($f);

	
} else {
	die("Batch filename didn't specified, use in request: 

exec.php?f=batchfile.bat&min=1&&bg=1 

where 'min' and 'bg' arguments are optional 
if 'min' set and true the opened window is minimized");
}