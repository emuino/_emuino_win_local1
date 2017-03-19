<?php
$term = isset($_GET['term']) ? $_GET['term'] : null;
$rets = array();
if($term) {
	$dir = scandir('../emuino/sketch');
	foreach($dir as $file) {
		if(substr($file, -4) == '.ino' && strpos($file, $term)!==false) {
			$rets[] = $file;
		}
	}
}

echo json_encode($rets);
