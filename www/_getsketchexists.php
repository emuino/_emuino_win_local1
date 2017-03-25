<?php
if($f = @$_GET['f']) {
    if(file_exists('../emuino/sketch/'.$f)) {
        die('OK');
    }
    die('ERROR: File not found');
}
else {
    die('ERROR: Filename did not set');
}