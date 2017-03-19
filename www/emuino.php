<!DOCTYPE html>
<html>
<head>
<meta charset="utf8">
<title>Emuino</title>
<link rel="stylesheet" href="jquery-ui-1.12.1.custom/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="emuino.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="jquery-ui-1.12.1.custom/jquery-ui.js"></script>
<script src="emuino.js<?php // echo "?_nocache=".rand(1000000,9999999); ?>"></script>
</head>
<body onbeforeunload="emuino.close();" onunload="emuino.close();">
	<ul class="emu-toolbar">
		<li class="emu-button" style="background-image: url(images/arduino.png);" onclick="emuino.start();">
		<li class="emu-button" style="background-image: url(images/exit.png); float:right;" onclick="emuino.close();">
	</li>
	</ul>
	<div class="dnd-container"></div>
	<div class="emu-stat"></div>
</body>
</html>