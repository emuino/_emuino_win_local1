@echo off

rem del c:\emuino\www\shell\win\_tmp_*.*

Taskkill /IM emuino.exe /F
cd C:\emuino\emuino
set PATH=%PATH%;c:\Program Files\Dev-Cpp\MinGW64\bin\
rem start /min "" 
mingw32-make.exe -f "C:\emuino\emuino\Makefile.win" clean all  >> c:\emuino\www\compile.log 2>&1

exit