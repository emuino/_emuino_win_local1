@echo off

rem -------------------------------------- stop

echo Kill all task..
Taskkill /IM wwebserver_cmd.exe /F
Taskkill /IM wwebserver.exe /F
Taskkill /IM websocketd.exe /F
Taskkill /IM emuino.exe /F
Taskkill /IM wssrv.exe /F

echo Clean up files..
del C:\emuino\pipe_*.

rem -------------------------------------- start

echo Start websocket server..
start /min "" C:\emuino\websocketd-0.2.11-windows_386\websocketd.exe --port=8080 wssrv/wssrv.exe

echo Start webserver..
cd C:\emuino\WWebserver_with_PHP_5.4.45\
start /min "" wwebserver_cmd.exe

rem pause for 2 seconds
@ping -n 2 127.0.0.1 >nul

echo Open browser client..
start /min "" http://localhost/emuino.php

exit
