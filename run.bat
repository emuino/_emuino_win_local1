@echo EMUINO - emulation and debugging environment
start wsd.bat
cd WWebserver_with_PHP_5.4.45
start wwebserver_cmd.exe
cd ..
start "" http://localhost/emuino.html

