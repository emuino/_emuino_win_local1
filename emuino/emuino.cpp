#include <stdio.h>
#include <stdlib.h>
#include <conio.h>
#include <stdarg.h>
#include <time.h>

// TODO: add it to the wiki
#define SKATCH "skatch/skatch.ino"

// TODO: emulated Arduino device type definition
#define __AVR_ATxmega384D3__
// todo measure the CPU speed or just rewrite the delay.h tipical F_CPU values e.g F_CPU=8000000 or F_CPU=1000000UL
#define F_CPU -1
#include <avr/variants/standard/pins_arduino.h>
#include <avr/cores/arduino/Arduino.h>

class EmuinoFileHandler {
public:
	
	void fappendln(char* fname, char* msg) {
		FILE* f = fopen(fname, "a");
		fprintf(f, "%s\n", msg);
		fclose(f);		
	}
	
	long fsize(char* fname) {
		FILE* f = fopen(fname, "r");
		fseek(f, 0L, SEEK_END);
		long size = ftell(f);
		fclose(f);
		return size;
	}
	
} emuFileHandler;



class EmuinoLogger {
public:
	
	void log(char* msg) {
		printf("%s\n", msg);		
		emuFileHandler.fappendln("emuino.log", msg);
	}
	
} emuLogger;



class EmuinoPipe {
public:
	
	void cliCmd(char* cmd) {
		emuLogger.log("[pipe cli cmd]: run:");
		emuLogger.log(cmd);
		// todo: run client command..
		
	}
	
	void send(char* msg) {
		emuLogger.log("[pipe send]: start:");
		emuLogger.log(msg);
		emuFileHandler.fappendln("../pipe_srv", msg);
		while(emuFileHandler.fsize("../pipe_srv"));
		emuLogger.log("[pipe send]: finish");
	}
	
	void sendf (const char * format, ... ) {
		char buffer[256];
		va_list args;
		va_start (args, format);
		vsprintf (buffer,format, args);
		send (buffer);
		va_end (args);
	}
	
	void read() {	
		//log("[pipe read]: start");	
		FILE* f = fopen("../pipe_cli", "r+");
		if(f!=NULL) {
			char line[255];
			while (fgets(line, sizeof(line), f)) {
				cliCmd(line);
			}
			fclose(f);		
			fclose(fopen("../pipe_cli", "w"));
			//log("[pipe read]: finish");
		}
	}
	
} emuPipe;




class Skatch {
public:
#include SKATCH
} skatch;




#define EMUINO_PINS 32

class Emuino {
private:

	int guid;
	
	int pins[EMUINO_PINS];
	
	void setPin(int pin, int value) {
		pins[pin] = value;
		emuPipe.sendf("devices.Arduino[%d].setPin(%d, %d);", guid, pin, value);
	}
	
	void reset() {
		emuLogger.log("[arduino]: reset..");
		// todo: reset pins and all of emulated arduino
		for(int i=0; i<EMUINO_PINS; i++) {
			setPin(i, 0);
		}
	}
	

public:
	Emuino() {
		srand(time(NULL));
		guid = rand();
		emuLogger.log("[EMUINO] start");
		emuPipe.sendf("emuino.make('Arduino', '%d', {});", guid);
		reset();
		emuLogger.log("[emuino skatch]: setup..");
		skatch.setup();
		emuLogger.log("[emuino skatch]: loop start.. (press a key to stop)");
		while(!kbhit()) {
			emuPipe.read();
			skatch.loop();
		}
		emuLogger.log("[EMUINO] halt");
		getch();
		getch();
	}
	
	~Emuino() {		
		emuPipe.sendf("emuino.remove('Arduino', '%d', {});", guid);
		emuLogger.log("[EMUINO] end");
	}
} emu;


/* run this program using the console pauser or add your own getch, system("pause") or input loop */

int main(int argc, char** argv) {
	
	return 0;
}


