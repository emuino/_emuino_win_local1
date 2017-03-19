#include <stdio.h>
#include <stdlib.h>
#include <conio.h>
#include <stdarg.h>
#include <time.h>

#define __EMU__

// TODO: add it to the wiki
#define SKETCH "sketch/sketch.ino"

// TODO: emulated Arduino device type definition
#define __AVR_ATxmega384D3__ //__DEVICE_TYPE__ - ************** DO NOT REMOVE THIS COMMENT! ITS NEED TO RE-PARSING THIS SOURCECODE !!! *****************
// todo measure the CPU speed or just rewrite the delay.h, interrupt.h etc.. tipical F_CPU values e.g F_CPU=8000000 or F_CPU=1000000UL
#define F_CPU 1000000UL

#include <avr/variants/standard/pins_arduino.h>
#include <avr/cores/arduino/Arduino.h>

// TODO change it if you need, Im not realy sure but I think it's related to Arduino device type
#include <avr/iocanxx.h>

#include <avr/cores/arduino/wiring.c>


class EmuinoFileHandler {
public:
	
	void fappendln(const char fname[], const char msg[]) {
		FILE* f = fopen(fname, "a");
		fprintf(f, "%s\n", msg);
		fclose(f);		
	}
	
	long fsize(const char fname[]) {
		FILE* f = fopen(fname, "r");
		fseek(f, 0L, SEEK_END);
		long size = ftell(f);
		fclose(f);
		return size;
	}
	
	void fclear(const char fname[]) {
		FILE* f = fopen(fname, "w");
		fclose(f);
	}
	
} emuFileHandler;



class EmuinoLogger {
public:
	
	void log(const char msg[]) {
		printf("%s\n", msg);		
		emuFileHandler.fappendln("emuino.log", msg);
	}
	
	void clear() {
		
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




class Sketch {
public:
#include SKETCH
} sketch;





class Emuino {
private:

	int id;
	
	int pinModes[NUM_DIGITAL_PINS+NUM_ANALOG_INPUTS];
	int pinValues[NUM_DIGITAL_PINS+NUM_ANALOG_INPUTS];
	
	
	void reset() {
		emuLogger.log("[arduino]: reset..");
		// todo: reset pins and all of emulated arduino
		for(int i=0; i<NUM_DIGITAL_PINS+NUM_ANALOG_INPUTS; i++) {
			setPinMode(i, 0);
			setPinValue(i, 0);
		}
	}
	

public:
	
	void setPinMode(int pin, int mode) {
		pinModes[pin] = mode;
		emuPipe.sendf("devices.Arduino[%d].setPinMode(%d, %d);", id, pin, mode);
	}
	
	void setPinValue(int pin, int value) {
		pinValues[pin] = value;
		emuPipe.sendf("devices.Arduino[%d].setPinValue(%d, %d);", id, pin, value);
	}
	
	Emuino() {
		emuLogger.clear();
		srand(time(NULL));
		id = rand();
		emuLogger.log("[EMUINO] start");
		emuPipe.sendf("emuino.make('Arduino', '%d', {});", id);
		reset();
		emuLogger.log("[emuino sketch]: setup..");
		sketch.setup();
		emuLogger.log("[emuino sketch]: loop start.. (press a key to stop)");
		while(!kbhit()) {
			emuPipe.read();
			sketch.loop();
		}
		emuLogger.log("[EMUINO] halt");
		getch();
		getch();
	}
	
	~Emuino() {		
		emuPipe.sendf("emuino.remove('Arduino', '%d', {});", id);
		emuLogger.log("[EMUINO] end");
	}
} emu;


#include <avr/cores/arduino/wiring_digital.c>


/* run this program using the console pauser or add your own getch, system("pause") or input loop */

int main(int argc, char** argv) {
	
	return 0;
}


