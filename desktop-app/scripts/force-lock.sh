#! /bin/sh

# Turn of the wifi and lock desktop
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

sleep 10
action
