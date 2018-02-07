#!/bin/bash

# Allowed timeout in milliseconds.
IDLE_TIME=$((10*1000))

sleep_time=$IDLE_TIME
triggered=false

# Action when timeout triggers.
action() {

	# Turn Off the Wifi
	if command nmcli radio >/dev/null 2>&1 ; then
	    nmcli radio wifi off
	else
	    nmcli nm wifi off
	fi

	# Lock the screen (Works for Unity and Gnome)
	dbus-send --type=method_call --dest=org.gnome.ScreenSaver /org/gnome/ScreenSaver org.gnome.ScreenSaver.Lock
}

# Detects Inactivity and triggers action,
check() {
	if command xprintidle >/dev/null 2>&1 ; then
	    while sleep $(((sleep_time+999)/1000)); do
	        idle=$(xprintidle)
	        if [ $idle -ge $IDLE_TIME ]; then
	            if ! $triggered; then
	                triggered=true
	                action
	                sleep_time=$IDLE_TIME
	            fi
	        else
	            triggered=false
	            sleep_time=$((IDLE_TIME-idle+100))
	        fi
	    done
	else
	    echo "Error: Install xprintidle first (sudo apt-get install xprintidle)"
	    exit
	fi
}

# Script running in infinite loop.
while true
do
	check
done
