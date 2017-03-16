@echo EMUINO - emulation and debugging environment
del pipe_*.
start websocketd-0.2.11-windows_386\websocketd.exe --port=8080 wssrv/wssrv.exe
cd WWebserver_with_PHP_5.4.45
start wwebserver_cmd.exe
cd ..
start "" http://localhost/emuino.html

