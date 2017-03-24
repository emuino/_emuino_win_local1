cd c:\emuino\emuino
rem del c:\emuino\www\compile.log
set PATH=%PATH%;c:\Program Files\Dev-Cpp\MinGW64\bin\
c:\emuino\emuino\compile.bat outlog
rem g++.exe -D__DEBUG__ -c emuino.cpp -o emuino.o -I"C:/Program Files/Dev-Cpp/MinGW64/include" -I"C:/Program Files/Dev-Cpp/MinGW64/x86_64-w64-mingw32/include" -I"C:/Program Files/Dev-Cpp/MinGW64/lib/gcc/x86_64-w64-mingw32/4.9.2/include" -I"C:/Program Files/Dev-Cpp/MinGW64/lib/gcc/x86_64-w64-mingw32/4.9.2/include/c++" -Og -m32 -pg -g3 -IArduino/hardware/arduino -IArduino/hardware/tools/avr/avr/include -IArduino/hardware/tools/avr/avr/include/avr -IArduino/hardware/arduino/avr/variants/standard >> c:\emuino\www\compile.log 2>&1
exit
