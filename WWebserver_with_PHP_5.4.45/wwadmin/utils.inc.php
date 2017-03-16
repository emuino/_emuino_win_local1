<?php


function getParam ($key, $default="") {
	return isset ($_GET[$key])  ? $_GET[$key]  : (isset ($_POST[$key])  ? $_POST[$key]  : $default );
}

function startsWith($haystack, $needle) {
   $length = strlen($needle);
   return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}

function getArrayValue ($array, $key, $default="", $subKey="") {
	if (isset ($array[$key])) {
		if ($subKey!="") {	
			if (isset ($array[$key][$subKey]))
				return $array[$key][$subKey];
			else
				return $default;
		}
		return $array[$key];		
	}
	return $default;	
}

function multiexplode ($delimiters,$string) {    
  $ready = str_replace($delimiters, $delimiters[0], $string);
  $launch = explode($delimiters[0], $ready);
  return  $launch;
}


function readLastLinesOfFile($filePath, $lines = 10) {
  //global $fsize;
  $handle = fopen($filePath, "r");
  if (!$handle) {
      return array();
  }
  $linecounter = $lines;
  $pos = -2;
  $beginning = false;
  $text = array();
  while ($linecounter > 0) {
      $t = " ";
      while ($t != "\n") {
          if(fseek($handle, $pos, SEEK_END) == -1) {
              $beginning = true;
              break;
          }
          $t = fgetc($handle);
          $pos--;
      }
      $linecounter--;
      if ($beginning) {
          rewind($handle);
      }
      $line = str_replace(array("\r", "\n", "\t"), '', fgets($handle));
      if (!empty ($line))
	      $text[$lines-$linecounter-1] = $line;
      if ($beginning) break;
  }
  fclose($handle);
  return $text;
}

function formatKBText ($sizeBytes) {
	if ($sizeBytes>=(1048576*1024))
		return round ($sizeBytes / (1048576*1024), 1)." GB";
	else
	if ($sizeBytes>=1048576)
		return round ($sizeBytes / 1048576, 1)." MB";
	else	
	if ($sizeBytes>=1024)
		return round ($sizeBytes / 1024)." KB";		
	else
		return $sizeBytes." Byte";		
}


function readKeyValueFile ($filename)
{
	$list = array ();
	$fh = fopen ($filename, "r");		
	if ($fh) {
		$idx = 0;	
		while (!feof($fh)) {				
	    $line = trim (fgets($fh), "\r\n ");
	    if (empty ($line))
				continue;
	    if (startsWith ($line, "#")) {
				continue;
	    }
	    if (strpos ($line, "=")===false)
	    	continue;
	    list ($key, $value)=explode ("=", $line);
	   	$list[trim ($key)]=trim ($value);
		}
		fclose ($fh);		
	}
	return $list;
}
 


?>