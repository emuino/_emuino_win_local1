Taskkill /IM wwebserver_cmd.exe /F
Taskkill /IM wwebserver.exe /F
Taskkill /IM websocketd.exe /F
Taskkill /IM emuino.exe /F
Taskkill /IM wssrv.exe /F

@echo All process killed
@ping -n 2 127.0.0.1 >nul

del C:\emuino\pipe_*.
start /min "" C:\emuino\websocketd-0.2.11-windows_386\websocketd.exe --port=8080 wssrv/wssrv.exe

cd C:\emuino\WWebserver_with_PHP_5.4.45\
start /min "" wwebserver_cmd.exe
rem start /min "" wwebserver.exe

@ping -n 5 127.0.0.1 >nul
start /min "" http://localhost/emuino.php
exit