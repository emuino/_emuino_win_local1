#include <conio.h>

// TODO: add it to the wiki
#define SKATCH "skatch/skatch.ino"

class ArduinoSkatch {
private:
#include SKATCH
public:
	ArduinoSkatch() {		
		setup();
		while(!kbhit()) {
			loop();
		} 
	}
} skatch;


/* run this program using the console pauser or add your own getch, system("pause") or input loop */

int main(int argc, char** argv) {
	
	return 0;
}


