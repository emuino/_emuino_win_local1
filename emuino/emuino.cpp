#include <stdio.h>
#include <stdlib.h>
#include <conio.h>
#include <stdarg.h>
#include <time.h>

// TODO: add it to the wiki
#define SKATCH "skatch/skatch.ino"

class Skatch {
public:
#include SKATCH
} skatch;

class Emuino {
private:

	int guid;
	
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
	
	void pipeSendf (const char * format, ... ) {
		char buffer[256];
		va_list args;
		va_start (args, format);
		vsprintf (buffer,format, args);
		pipeSend (buffer);
		va_end (args);
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
	Emuino() {
		srand(time(NULL));
		guid = rand();
		log("[EMUINO] start");
		pipeSendf("make('Arduino', '%d', {});", guid);
		reset();
		log("[emuino skatch]: setup..");
		skatch.setup();
		log("[emuino skatch]: loop start..");
		while(!kbhit()) {
			pipeRead();
			skatch.loop();
		}
	}
	
	~Emuino() {		
		pipeSendf("remove('Arduino', '%d', {});", guid);
		log("[EMUINO] end");
	}
} emu;


/* run this program using the console pauser or add your own getch, system("pause") or input loop */

int main(int argc, char** argv) {
	
	return 0;
}


