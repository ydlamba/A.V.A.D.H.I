/**
 * CClient Implementation
 * Listens to UDP broadcasts on port 7447
 * And sends mac_address using TCP to port 7777
 * For debugging purposes use the command
 * gcc -Wall client.c
 */

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <sys/ioctl.h>
#include <net/if.h>
#include <unistd.h>
#include <errno.h>
#include <arpa/inet.h>
#include <netinet/in.h>
#include <netdb.h>


#define BUFLEN       512
#define SERVER_ADDR  ""
#define SERVER_PORT  7777
#define PORT         7447

unsigned char mac_address[6] = {0};
char IP_ADDR[50];

void die(char *s) {
    perror(s);
    exit(1);
}

int display_error(char *s) {
    perror(s);
    return 1;
}

int isValidIpAddress(char *ipAddress) {
    struct sockaddr_in sa;
    int result = inet_pton(AF_INET, ipAddress, &(sa.sin_addr));
    return result;
}

char *get_client_mac_address() {
    struct ifreq ifr;
    struct ifconf ifc;
    char buf[BUFLEN];
    int success = 0, sock;

    if ((sock = socket(PF_INET, SOCK_DGRAM, IPPROTO_IP)) == -1) {
        die("[-] Socket creation failed");

    }
    ifc.ifc_len = sizeof(buf);
    ifc.ifc_buf = buf;
    if (ioctl(sock, SIOCGIFCONF, &ifc) == -1)
        die("[-] Could not use IOCTL");

    struct ifreq* it = ifc.ifc_req;
    const struct ifreq* const end = it + (ifc.ifc_len / sizeof(struct ifreq));

    for (; it != end; ++it) {
        strcpy(ifr.ifr_name, it->ifr_name);
        if (ioctl(sock, SIOCGIFFLAGS, &ifr) == 0)
            if (! (ifr.ifr_flags & IFF_LOOPBACK)) // don't count loopback
                if (ioctl(sock, SIOCGIFHWADDR, &ifr) == 0) {
                    success = 1;
                    break;
                }
        else
            die("[-] Error while looping through interfaces");
    }

    if (success)
        memcpy(mac_address, ifr.ifr_hwaddr.sa_data, 6);
    
    close(sock);
    printf("> Successfully received Local MAC Address : %02x:%02x:%02x:%02x:%02x:%02x\n",
        (unsigned char) mac_address[0],
        (unsigned char) mac_address[1],
        (unsigned char) mac_address[2],
        (unsigned char) mac_address[3],
        (unsigned char) mac_address[4],
        (unsigned char) mac_address[5]);
    return (char *) &mac_address; 
}

int send_mac_address_to_server(char *mac_address) {
    struct sockaddr_in sock_addr;
    int sock_size = sizeof(sock_addr);
    int sock_desc, recv_len;
    char buf[BUFLEN];

    if ((sock_desc = socket(PF_INET, SOCK_STREAM, IPPROTO_IP)) == -1) {
        display_error("Socket creation failed");
        return 1;
    }

    memset((char *) &sock_addr, 0, sizeof(sock_addr));
    sock_addr.sin_family = AF_INET;
    sock_addr.sin_port = htons(SERVER_PORT);
    sock_addr.sin_addr.s_addr = inet_addr(SERVER_ADDR);

    if (connect(sock_desc, (struct sockaddr*) &sock_addr, sock_size) == -1)
        return display_error("TCP socket bind failed");

    struct timeval tv;
    tv.tv_sec = 20;  /* 20 Secs Timeout */
    tv.tv_usec = 0;

    if(setsockopt(sock_desc, SOL_SOCKET, SO_SNDTIMEO, (char *)&tv,sizeof(tv)) < 0)
      return display_error("[-] Timed out while connecting to server");

    if(send(sock_desc, mac_address, strlen(mac_address), 0) == -1)
        return display_error("[-] Could not send mac_address to server");
}

int main(int argc, char *argv[]) {
    if (argc != 2 && !isValidIpAddress(SERVER_ADDR)) {
        printf("[-] You need to provide an IP address to continue ... \n\n");
        exit(1);
    }
    if (argc == 2) {
        if (!isValidIpAddress(argv[1])) {
            printf("[-] Not a valid  IP address");
            exit(1);
        }
        strcpy(IP_ADDR, argv[1]);
    }

    char *mac_address = get_client_mac_address();

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
        printf("[*] Waiting for the data ...");
        fflush(stdout);
        /* Recieving data, blocking call */
        if ((recv_len = recvfrom(sock_desc,
                                buf,
                                BUFLEN,
                                0,
                                (struct sockaddr *) &sock_addr,
                                &sock_size)) == -1 ) {
            display_error("Error in recieving ...");
            continue;
        }
        printf("Data received from server is :: %s \n", buf);
        send_mac_address_to_server(mac_address);
    }

    return 0;
}
