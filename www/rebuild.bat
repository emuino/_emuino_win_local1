Taskkill /IM emuino.exe /F
cd C:\emuino\emuino
set PATH=%PATH%;c:\Program Files\Dev-Cpp\MinGW64\bin\
start /min "" mingw32-make.exe -f "C:\emuino\emuino\Makefile.win" clean all
rem pause
exit
