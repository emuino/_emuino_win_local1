#include <stdio.h>
#include <unistd.h>
#include <iostream>
#include <string>
#include <thread>
#include <mutex>

using namespace std;

mutex m;
string *msg;

void in()
{
    while(true)
    {
        for (string line; getline(cin, line);)
        {
            m.lock();
            msg = new string("RCVD: ");
            *msg += line;
            m.unlock();
        }
    }
}

void out()
{
    while(true)
    {
        m.lock();
        if (msg)
        {
            cout << "You sent me: " << *msg << endl;
            msg = 0;
        }
        m.unlock();
        //usleep(1000000);
    }
}

int main() {
    // Disable input/output buffering.
    setbuf(stdout, NULL);
    setbuf(stdin, NULL);

    thread inThread(in);
    thread outThread(out);

    inThread.join();
    outThread.join();

    return 0;
}
