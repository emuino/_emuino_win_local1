<?php

class ECShellException extends Exception {
	
	public function __construct($msg, $e = null, $p = null) {
		parent::__construct('Emuino Call, Shell Runing Exception: '.$msg, $e, $p);
	}
	
}

class EmuinoCalls {
	
	private static $stats = array(
		'S' => 'S',
		'SUCC' => 'S',
		'SUCCESS' => 'S',
		'W' => 'W',
		'WARN' => 'W',
		'WARNING' => 'W',
		'E' => 'E',
		'ERR' => 'E',
		'ERROR' => 'E',
	);

	private function resp($results = true, $message = 'OK', $status = 'S') {
		
		return array(
			'results' => $results,
			'message' => $message,
			'status' => self::$stats[strtoupper($status)],
		);
	}
	
	private function respE($message = 'Error occurred', $additionalInfo = false) {
		if($message instanceof Exception) {
			return $this->respException($message);
		}
		return $this->resp($additionalInfo, $message, 'E');
	}
	
	private function respException(Exception $e) {
		$emsg = $e->getMessage();
		return $this->respE('Exception message was: '.$emsg, array(
			'ExceptionMessage' => $e->getMessage(),
			'ExceptionCode' => $e->getCode(),
			'ExceptionPrev' => $e->getPrevious(),
			'ExceptionObject' => $e,
		));
	}
	
	
	// ---
	
	private function getOS() {
		if(preg_match('/^win/i', PHP_OS)) return 'WINDOWS';
		if(preg_match('/^linux/i', PHP_OS)) return 'LINUX';
		return 'unknown operating system';
	}
	
	private function runShell($cmd, $min = true) {
		
		switch($this->getOS()) {
			case 'WINDOWS':
				$f = trim(explode(' ', $cmd)[0]);
				
				$f = "c:\\emuino\\www\\shell\\win\\{$f}.bat";
			
				if(!file_exists($f)) {
					throw new ECShellException("file not found: $f");
				}
				
				if(pathinfo($f, PATHINFO_EXTENSION) != 'bat') {
					throw new ECShellException("only .bat files available: $f is incorrect");
				}
				
				if(pathinfo(realpath($f), PATHINFO_DIRNAME) != pathinfo(realpath(__FILE__), PATHINFO_DIRNAME)."\\shell\\win") {
					throw new ECShellException("requested folder path disabled: $f is incorrect");
				}
						
				$f = "start " . ($min ? "/min \"\" " : "") . "$f"; 
				
				$output = shell_exec($f);
				return $output;
			break;
			case 'LINUX':
				throw new ECShellException("Sorry, Linux doesn't supported yet.. :(");
			break;
			default:
				throw new ECShellException("Unknown operating system");
			break;
		}

	}
	
	// todo: separate extensions!
	
	//-- SketchEditor

	public function getSketches($args) {	
		$rets = array();

		$dir = scandir('../emuino/sketch');
		foreach($dir as $file) {
			if(substr($file, -4) == '.ino' && (strpos($file, $_GET['term'])!==false || 1)) {
				$rets[] = $file;
			}
		}
		
		return $rets;
	}
	
	public function getSketchExists($args) {
		if($f = $args[0]) {
			if(file_exists('../emuino/sketch/'.$f)) {
				if(!preg_match('/\.ino$/', $f)) {
					return $this->resp(true, 'File extension is not .ino', 'W');
				}
				return $this->resp();
			}
			return $this->respE('File not found');
		}
		return $this->respE('Filename did not set');
	}
	
	public function getSketchContents($args) {
		if($f = $args[0]) {
			$f = '../emuino/sketch/'.$f;
			if(file_exists($f)) {
				return $this->resp(true, file_get_contents($f));
			}
			return $this->respE('Sketch file not found: '. $f);
		}		
		return $this->respE('Filename not set');
	}
	
	public function doSketchRepair($args) {
		$device = $args[0];
		$sketch = $args[1];

		if(!$sketch) {
			return $this->respE('skatch is not defined');
		}
		if(!$device) {
			return $this->respE('device is not defined');
		}		
		if(count(explode('/', $sketch)) > 1 || count(explode('\\', $sketch)) > 1) {
			return $this->respE('subfolder detected in filename');
		}
		if(!preg_match('/.ino$/', $sketch)) {
			return $this->respE('only .ino extension acceptable');
		}

		$cfile = '../emuino/emuino.cpp';

		$c = file_get_contents($cfile);

		$c = preg_replace('/^\#define\s+SKETCH\s+\"sketch\/([^\"]+)\"/m', '#define SKETCH "sketch/'.$sketch.'"', $c);
		$c = preg_replace('/^\#define\s+([^\s]+)\s*\/\/\s*__DEVICE_TYPE__/m', '#define '.$device.' //__DEVICE_TYPE__', $c);

		if(!file_put_contents($cfile, $c)) {
			return $this->respE('file write error: '.$cfile);
		}

		return $this->resp();
	}
	
	// start emuino
	
	public function doRunArduino($args) {
		unlink('compile.log');
		$resp = $this->doSketchRepair($args);
		if($resp["status"]!='S') {
			return $resp;
		}
		try {
			$this->runShell('compile');
			switch($this->getOS()) {
				case 'WINDOWS':
					$execf = "C:\\emuino\\emuino\\emuino.exe";
					if(!file_exists($execf)) {
						return $this->respE('Compile error', htmlentities(file_get_contents('compile.log')));
					}
					//system("Taskkill /IM emuino.exe /F");
					popen("start /min \"\" {$execf}", 'r');
					return $this->resp(file_get_contents('compile.log'));
				break;
				case 'LINUX':
					return $this->respE('Sorry, Linux doesn\'t supported for this function in this version :( compilation failed.');
				break;
				default:
					return $this->respE('Unknown operating system, compilation failed');
				break;
			}
		} catch (ECShellException $e) {
			return $this->respE($e);
		}
	}

}

if(isset($_GET['func']) && $_GET['func']) {

	$call = $_GET['func'];
	if(!preg_match('/^[a-zA-Z_]+[a-zA-Z0-9_]*$/', $call)) {
		throw new Exception('illegal function name: ' . $call);
	}
	$args = isset($_GET['args']) ? $_GET['args'] : array('');
	$ec = new EmuinoCalls();
	$rets = $ec->$call($args);
	echo json_encode($rets);
	
} else {
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf8">
<title>Emuino</title>
<link rel="shortcut icon" type="image/png" href="favicon.png"/>
<link rel="stylesheet" href="jquery-ui-1.12.1.custom/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="emuino.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="jquery-ui-1.12.1.custom/jquery-ui.js"></script>
<script src="ace/build/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="emuino.js<?php //echo "?_nocache=".rand(1000000,9999999); // TODO: remove this for keep browser cache ?>"></script>
</head>
<body onbeforeunload="emuino.close();" onunload="emuino.close();">
	<ul class="emu-toolbar">
		<li class="emu-button" style="background-image: url(images/arduino.png);" onclick="emuino.start();">
		<li class="emu-button" style="background-image: url(images/editor.png);" onclick="emuino.make('SketchEditor');">
		<li class="emu-button" style="background-image: url(images/exit.png); float:right;" onclick="emuino.close();">
	</li>
	</ul>
	<div class="dnd-container"></div>
	<div class="emu-stat"></div>
</body>
</html>
<?php } 
