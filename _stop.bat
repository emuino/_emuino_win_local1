Taskkill /IM wwebserver_cmd.exe /F
Taskkill /IM wwebserver.exe /F
Taskkill /IM websocketd.exe /F
Taskkill /IM emuino.exe /F
Taskkill /IM wssrv.exe /F


@echo All process killed
@ping -n 2 127.0.0.1 >nul
exit