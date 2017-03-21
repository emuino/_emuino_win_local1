<?php
if($f = @$_GET['f']) {
    $f = '../emuino/sketch/'.$f;
    if(file_exists($f)) {
        echo file_get_contents($f);
    }
}
exit();