cd C:\emuino

del C:\emuino\pipe_*.
start /min "" C:\emuino\websocketd-0.2.11-windows_386\websocketd.exe --port=8080 wssrv/wssrv.exe

@echo WSD restart...
@ping -n 5 127.0.0.1 >nul
exit