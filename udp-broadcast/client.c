#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <errno.h>
#include <arpa/inet.h>
#include <netinet/in.h>
#include <netdb.h>


#define BUFLEN   512
#define PORT     7447

void die(char *s) {
    perror(s);
    exit(1);
}

int main() {
    struct sockaddr_in sock_addr;
    int sock_size = sizeof(sock_addr);
    int sock_desc, recv_len;
    char buf[BUFLEN];

    if ((sock_desc = socket(PF_INET, SOCK_DGRAM, IPPROTO_UDP)) == -1)
        die("Socket creation failed");

    memset((char *) &sock_addr, 0, sizeof(sock_addr));
    sock_addr.sin_family = AF_INET;
    sock_addr.sin_port = htons(PORT);
    sock_addr.sin_addr.s_addr = htonl(INADDR_ANY);

    if (bind(sock_desc, (struct sockaddr*) &sock_addr, sock_size) == -1)
        die("Error in bind");

    while (1) {
        printf("Waiting for the data");
        fflush(stdout);
        /* Recieving data, blocking call */
        if ((recv_len = recvfrom(sock_desc, buf, BUFLEN, 0, (struct sockaddr *) &sock_addr, &sock_size)) == -1 )
            die("Error in recieving ...");
        printf("Data received from server is :: %s \n", buf);
    }

    return 0;
}
