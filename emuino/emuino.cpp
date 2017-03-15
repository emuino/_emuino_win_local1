#include <stdio.h>
#include <stdlib.h>
#include <conio.h>
#include <stdarg.h>
#include <time.h>

// TODO: add it to the wiki
#define SKATCH "skatch/skatch.ino"


class EmuinoFileHandler {
public:
	
	void fappendln(char* fname, char* msg) {
		FILE* f = fopen(fname, "a");
		fprintf(f, "%s\n", msg);
		fclose(f);		
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

class Emuino {
private:

	int guid;
	

	
	void reset() {
		emuLogger.log("[arduino]: reset..");
		// todo: reset pins and all of emulated arduino
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
		emuLogger.log("[emuino skatch]: loop start..");
		while(!kbhit()) {
			emuPipe.read();
			skatch.loop();
		}
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


