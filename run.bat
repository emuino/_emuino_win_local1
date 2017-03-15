@echo EMUINO - emulation and debugging environment
@pause
del pipe_log
del pipe_lock
start websocketd\websocketd-0.2.11-windows_386\websocketd.exe --port=8080 wssrv/wssrv.exe
start miniweb\miniweb.exe
start "" http://localhost:8000/emuino/emuino.html

