<?php

//start /min "" C:\emuino\emuino\emuino.exe
//exit
system("Taskkill /IM emuino.exe /F");
popen("start /min \"\" C:\\emuino\\emuino\\emuino.exe", 'r');