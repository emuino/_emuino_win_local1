<?php

if($f = @$_GET['f']) {
  $data = @$_REQUEST['data'];
  if(!file_put_contents('../emuino/sketch/'.$f, $data)) {
      die('ERROR: File write error');
  }
  die('OK');
}
die('ERROR: File not set');