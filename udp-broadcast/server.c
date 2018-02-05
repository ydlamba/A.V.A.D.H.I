/**
 * Server Implementation - UDP broadcaster
 * Broadcasts UDP on the network on the port 7447
 * And listens to TCP on port 7777
 */

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <errno.h>
#include <arpa/inet.h>
#include <netinet/in.h>
#include <netdb.h>
#include <sys/time.h>
#include <fcntl.h>
#include <event2/event.h>
#include <event2/event_struct.h>
#include <event2/bufferevent.h>
#include <event2/buffer.h>
#include <pthread.h>


#define BUFLEN           512
#define BROADCAST_PORT   7447
#define LISTEN_PORT      7777
#define BROADCAST_ADDR   "255.255.255.255"
#define BROADCAST_DELAY  10
#define MAX_TCP_CONN     10


static struct event_base *evbase;

struct client {
    int fd;
    struct bufferevent *buf_ev;
};


/**
 * Die showing the messege in s and error code
 */
void die(char *s) {
    perror(s);
    exit(1);
}

int display_error(char *s) {
    perror(s);
    return 1;
}

/* Set a socket to non blocking mode */
int setnonblock(int fd)
{
    int flags;

    flags = fcntl(fd, F_GETFL);
    if (flags < 0)
        return flags;
    flags |= O_NONBLOCK;
    if (fcntl(fd, F_SETFL, flags) < 0)
        return -1;

    return 0;
}


void udp_broadcast(int fd, short event, void *arg) {
    int sock_desc;
    int broadcast_permission = 1;

    char *broadcast_message = "discover 7777";

    struct sockaddr_in sock_addr;
    socklen_t sock_size = sizeof(sock_addr);
    
    /**
     * Open a socket in your system, A UDP socket using IPv4 address
     * PF_INET because I read somewhere to prefrebly use it in place 
     * of AF_INET for socket-descriptor creator.
     */
    if ((sock_desc = socket(PF_INET, SOCK_DGRAM, IPPROTO_UDP)) == -1)
        display_error("[-] UDP socket creation Error");

    int set_sock_opt = setsockopt(sock_desc,
                                    SOL_SOCKET,
                                    SO_BROADCAST,
                                    &broadcast_permission,
                                    sizeof(broadcast_permission));
    if (set_sock_opt == -1)
        display_error("[-] Error in setting udp socks options");
    
    memset((char *) &sock_addr, 0, sock_size);
    sock_addr.sin_family = AF_INET;
    sock_addr.sin_port = htons(BROADCAST_PORT);

    /* Converts a dot seperated IP address to network byte order */
    if (inet_aton(BROADCAST_ADDR, &sock_addr.sin_addr) == 0)
        display_error("[-] Error in Broadcast IP");
   
    if (sendto(sock_desc,
                broadcast_message,
                strlen(broadcast_message),
                0,
                (struct sockaddr *) &sock_addr,
                sock_size) == -1) {
        display_error("Error in sendto() function");
    }

    printf("[+] Discover messege broadcasted successfully\n");
    
    // Close the socket descriptor
    close(sock_desc);
}


void *run_upd_broadcast_server(void * param) {
    /**
     * Creating a timer using libevent2 timer
     * It runs the function udp_broadcast every BROADCAST_DELAY seconds
     */ 
    printf("[*] Server up and running ...\n\n");
    struct event_base *ev_base = event_base_new();
    struct event *ev;
    struct timeval tv = { BROADCAST_DELAY, 0 };

    ev = event_new(ev_base, -1, EV_PERSIST, udp_broadcast, NULL);
    event_add(ev, &tv);
    event_base_dispatch(ev_base);

    printf("After event base dispatch \n");
    return NULL;
}

/**
 * Called by libevent when there is data to read.
 * from the client
 */
void
read_from_client(struct bufferevent *bev, void *arg)
{
    uint8_t data[BUFLEN];
    size_t n;

    for (;;) {
        n = bufferevent_read(bev, data, sizeof(data));
        if (n <= 0)
            break;
    }
    printf("[*] Successfully received Local MAC Address"
        ": %02x:%02x:%02x:%02x:%02x:%02x\n",
        (unsigned char) data[0],
        (unsigned char) data[1],
        (unsigned char) data[2],
        (unsigned char) data[3],
        (unsigned char) data[4],
        (unsigned char) data[5]);
}


void
client_read_error(struct bufferevent *bev, short what, void *arg)
{
    struct client *client = (struct client *)arg;

    if (what & BEV_EVENT_EOF)
        /* Client disconnected, remove the read event and the
         * free the client structure. */
        printf("A client disconnected.\n");
    else {
        display_error("Client socket error, disconnecting.\n");
        return;
    }

    bufferevent_free(client->buf_ev);
    close(client->fd);
    free(client);
}


void tcp_acceptor(int fd, short ev, void *arg) {
    int client_fd;
    struct sockaddr_in client_addr;
    socklen_t client_len = sizeof(client_addr);
    struct client *client;

    client_fd = accept(fd, (struct sockaddr *)&client_addr, &client_len);
    if (client_fd < 0) {
        display_error("TCP accept failed");
    }

    /* Set the client socket to non-blocking mode. */
    if (setnonblock(client_fd) < 0) {
        display_error("Failed to set client socket non-blocking");
    }
    client = calloc(1, sizeof(*client));
    if (client == NULL)
        display_error("Calloc failed");
    client->fd = client_fd;

    client->buf_ev = bufferevent_socket_new(evbase, client_fd, 0);
    bufferevent_setcb(client->buf_ev, read_from_client, NULL,
        client_read_error, client);

    bufferevent_enable(client->buf_ev, EV_READ);

    printf("Accepted connection from %s\n", 
        inet_ntoa(client_addr.sin_addr));
}


void *run_tcp_listener(void * param) {
    printf("[*] TCP server up and running ...\n");

    struct event ev_accept;
    evbase = event_base_new();

    struct sockaddr_in sock_addr;
    int sock_size = sizeof(sock_addr);
    int sock_desc;
    int allow = 1;

    if ((sock_desc = socket(PF_INET, SOCK_STREAM, IPPROTO_IP)) == -1) {
        display_error("Socket creation failed");
        return NULL;
    }

    if (setsockopt(sock_desc, SOL_SOCKET, SO_REUSEADDR, &allow, sizeof allow) == -1) {
        display_error("Error while setting sock options");
        return NULL;
    }

    memset((char *) &sock_addr, 0, sizeof(sock_addr));
    sock_addr.sin_family = AF_INET;
    sock_addr.sin_addr.s_addr = INADDR_ANY;
    sock_addr.sin_port = htons(LISTEN_PORT);

    if (bind(sock_desc, (struct sockaddr *)&sock_addr, sock_size) < 0) {
        display_error("Socket bind failed");
        return NULL;
    }

    if(listen(sock_desc, MAX_TCP_CONN) < 0) {
        display_error("Listen and setnonblock Error");
        return NULL;
    }

    if(setnonblock(sock_desc) < 0) {
        display_error("Setnonblock Error");
        return NULL;
    }

    event_assign(&ev_accept, evbase, sock_desc, EV_READ|EV_PERSIST, 
        tcp_acceptor, NULL);
    event_add(&ev_accept, NULL);
    /* Start the event loop. */
    event_base_dispatch(evbase);

    return NULL;
}


int main(int argc, char *argv[]) {
    pthread_t udp_thread, tcp_thread;
    pthread_create(&udp_thread, NULL, run_upd_broadcast_server, NULL);
    pthread_create(&tcp_thread, NULL, run_tcp_listener, NULL);

    pthread_join(udp_thread, NULL);
    pthread_join(tcp_thread, NULL);

    return 0;
}
