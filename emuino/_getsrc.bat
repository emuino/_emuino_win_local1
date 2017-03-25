@echo off
for /f "usebackq delims=" %%x in (`dir /s/b Arduino\hardware\arduino\avr\libraries`) do echo -I%%x
for /f "usebackq delims=" %%x in (`dir /s/b Arduino\libraries\src`) do echo -I%%x
pause
exit
