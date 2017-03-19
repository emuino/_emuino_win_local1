rem Taskkill /IM wwebserver_cmd.exe /F
Taskkill /IM websocketd.exe /F
Taskkill /IM emuino.exe /F
Taskkill /IM wssrv.exe /F

@echo WSD stopped
@ping -n 5 127.0.0.1 >nul
exit