CC=gcc

all: client server

client: client.c
	$(CC) -o client.o client.c

server: server.c utils.c utils.h
	$(CC) -o server.o server.c utils.c `mysql_config --libs` -L. -levent -levent_core -lpthread

clean:
	rm -f client.o server.o
