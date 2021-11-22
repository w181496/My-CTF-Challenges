#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include <signal.h>
void main() {
    seteuid(0);
    setegid(0);

    setuid(0);
    setgid(0);

    srand(time(NULL));
    
    signal(SIGALRM, exit);
    alarm(1);

    int a = rand() % 1000 + 5278;
    int b = rand() % 1000 + 87;
    int c;

    printf("%d + %d = ?\n", a, b);
    fflush(stdout);

    scanf("%d", &c);

    if(c == a + b) {
        system("/bin/cat /flag");
    } else {
        printf("G______G\n");
    }
}

