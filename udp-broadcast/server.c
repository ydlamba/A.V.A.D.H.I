#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <errno.h>
#include <arpa/inet.h>
#include <netinet/in.h>
#include <netdb.h>


#define BUFLEN           512
#define PORT             7447
#define BROADCAST_ADDR   "255.255.255.255"

/**
 * Die showing the messege in s and error code
 */
void die(char *s) {
    perror(s);
    exit(1);
}

int main(int argc, char *argv[]) {
    int sock_desc;
    int broadcast_permission = 1;
    struct sockaddr_in sock_addr;
    socklen_t sock_size = sizeof(sock_addr);
    char buffer[BUFLEN];
    
    /**
     * Open a socket in your system, A UDP socket using IPv4 address
     */
    if ((sock_desc = socket(PF_INET, SOCK_DGRAM, IPPROTO_UDP)) == -1)
        die("[-] Socket Error");
    printf("[+] Socket created successfully");

    int set_sock_opt = setsockopt(sock_desc, SOL_SOCKET, SO_BROADCAST, &broadcast_permission, sizeof(broadcast_permission));
    if (set_sock_opt == -1)
        die("Error in setsockopt");
    
    memset((char *) &sock_addr, 0, sock_size);
    sock_addr.sin_family = AF_INET;
    sock_addr.sin_port = htons(PORT);

    /* Converts a dot seperated IP address to network byte order */
    if (inet_aton(BROADCAST_ADDR, &sock_addr.sin_addr) == 0)
        die("Error in IPv4 address");
   
    char *broadcast_message = "This is a broadcast message";
    if (sendto(sock_desc, broadcast_message, strlen(broadcast_message), 0, (struct sockaddr *) &sock_addr, sock_size) == -1) {
        die("Error in sendto() function");
    }

    printf("[+] Messege broadcasted successfully");
    
    close(sock_desc);
    return 0;
}
