@echo off

rem -------------------------------------- start

echo Start websocket server..
start /min "" C:\emuino\websocketd-0.2.11-windows_386\websocketd.exe --port=8080 wssrv/wssrv.exe

echo Start webserver..
cd C:\emuino\WWebserver_with_PHP_5.4.45\
start /min "" wwebserver_cmd.exe

echo Open browser client..
start /min "" http://localhost/emuino.php

exit
