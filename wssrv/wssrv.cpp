#include <stdio.h>
#include <unistd.h>
#include <iostream>
#include <string>
#include <thread>
#include <mutex>
#include <fstream>
#include <streambuf>
#include <sstream>

using namespace std;

mutex m;

void fappend(string fname, string contents) {
	ofstream ofs;
	ofs.open(fname, ios_base::app);
	ofs << contents;
	ofs.close();
}

string freadall(string fname) {
	string ret;
	ifstream ifs(fname);
	stringstream buffer;
	buffer << ifs.rdbuf();
	ret = buffer.str();
	ifs.close();
	return ret;
}

void fclear(string fname) {
	ofstream ofs;
	ofs.open(fname, ofstream::out | ofstream::trunc);
	ofs.close();
}

void pipelog(string msg) {
	fappend("pipe_log", msg);
	fappend("pipe_log", "\n");
}

void in()
{
	pipelog("[in]: start");
    while(true)
    {
        for (string line; getline(cin, line);)
        {
        	
			pipelog("[in]: lock mutex and write out a message to pipe_cli:");
			pipelog(line);			
			line += '\n';
            m.lock();
            fappend("pipe_cli", line);
            m.unlock();
        }
    }
}

void out()
{
	string msg;
	pipelog("[out]: start");
    while(true)
    {
        m.lock();
        msg = freadall("pipe_srv");
        if (msg.length())
        {
        	pipelog("[out]: new message found in pipe_srv:");
        	pipelog(msg);
            cout << msg << endl;
	        pipelog("[out]: clear pipe_srv");
	        fclear("pipe_srv");
        }
        m.unlock();
        //usleep(1000000);
    }
}

int main() {
	
	pipelog("[main]: client connected, pipe handler start");
	
    // Disable input/output buffering.
    setbuf(stdout, NULL);
    setbuf(stdin, NULL);
    
    pipelog("[main]: check pipe_lock");
	string lock = freadall("pipe_lock");
	if(lock.length()) {
		pipelog("[main]: pipe locked (exit)");
		cout << "emuino.wsdRestart();" << endl;
		return -1;
	}
	
	pipelog("[main]: pipe open, lock pipe");
	
	fclear("pipe_lock");
	fappend("pipe_lock", "locked");

	pipelog("[main]: set threads (in,out)");

    thread inThread(in);
    thread outThread(out);

    inThread.join();
    outThread.join();

	pipelog("[main]: pipehangler finished");

    return 0;
}
