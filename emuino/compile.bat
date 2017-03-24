@echo off
rem del c:\emuino\www\compile.log
set myargs=
for /f "usebackq delims=" %%x in (`dir /s/b Arduino\libraries\src`) do call set myargs=%%myargs%% -I%%x
if "%1" == "outlog" (
	g++.exe %myargs% -D__DEBUG__ -c emuino.cpp -o emuino.o -I"C:/Program Files/Dev-Cpp/MinGW64/include" -I"C:/Program Files/Dev-Cpp/MinGW64/x86_64-w64-mingw32/include" -I"C:/Program Files/Dev-Cpp/MinGW64/lib/gcc/x86_64-w64-mingw32/4.9.2/include" -I"C:/Program Files/Dev-Cpp/MinGW64/lib/gcc/x86_64-w64-mingw32/4.9.2/include/c++" -Og -m32 -pg -g3 -IArduino/hardware/arduino -IArduino/hardware/tools/avr/avr/include -IArduino/hardware/tools/avr/avr/include/avr -IArduino/hardware/arduino/avr/variants/standard  >> c:\emuino\www\compile.log 2>&1
) else (
	g++.exe %myargs% -D__DEBUG__ -c emuino.cpp -o emuino.o -I"C:/Program Files/Dev-Cpp/MinGW64/include" -I"C:/Program Files/Dev-Cpp/MinGW64/x86_64-w64-mingw32/include" -I"C:/Program Files/Dev-Cpp/MinGW64/lib/gcc/x86_64-w64-mingw32/4.9.2/include" -I"C:/Program Files/Dev-Cpp/MinGW64/lib/gcc/x86_64-w64-mingw32/4.9.2/include/c++" -Og -m32 -pg -g3 -IArduino/hardware/arduino -IArduino/hardware/tools/avr/avr/include -IArduino/hardware/tools/avr/avr/include/avr -IArduino/hardware/arduino/avr/variants/standard
)
exit
