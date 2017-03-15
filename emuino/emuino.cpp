#include <stdio.h>
#include <stdlib.h>
#include <conio.h>

// TODO: add it to the wiki
#define SKATCH "skatch/skatch.ino"

class ArduinoSkatch {
private:
#include SKATCH

	
	void fappendln(char* fname, char* msg) {
		FILE* f = fopen(fname, "a");
		fprintf(f, "%s\n", msg);
		fclose(f);		
	}

	void log(char* msg) {
		printf("%s\n", msg);		
		fappendln("emuino.log", msg);
	}
	
	void pipeCliCmd(char* cmd) {
		log("[pipe cli cmd]: run:");
		log(cmd);
		// todo: run client command..
		
	}
	
	void pipeSend(char* msg) {		
		log("[pipe send]: start:");
		log(msg);
		fappendln("../pipe_srv", msg);
		log("[pipe send]: finish");
	}
	
	void pipeRead() {	
		//log("[pipe read]: start");	
		FILE* f = fopen("../pipe_cli", "r+");
		if(f!=NULL) {
			char line[255];
			while (fgets(line, sizeof(line), f)) {
				pipeCliCmd(line);
			}
			fclose(f);		
			fclose(fopen("../pipe_cli", "w"));
			//log("[pipe read]: finish");
		}
	}
	
	void reset() {
		log("[arduino]: reset..");
		// todo: reset pins and all of emulated arduino
	}
	
public:
	ArduinoSkatch() {
		log("[EMUINO] start");
		pipeSend("arduino.reset");
		reset();
		log("[arduino sketch]: setup..");
		setup();
		log("[arduino sketch]: loop start..");
		while(!kbhit()) {
			pipeRead();
			loop();
		}
	}
} skatch;


/* run this program using the console pauser or add your own getch, system("pause") or input loop */

int main(int argc, char** argv) {
	
	return 0;
}


