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

exit
