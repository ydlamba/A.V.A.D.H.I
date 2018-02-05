#! /bin/sh

if type $xprintidle > /dev/null; then
	echo "0: Installing xprintidle ..."
	sudo apt-get install xprintidle
else 
	echo "0: xprintidle Already Installed"
fi

echo "1: Giving executing permissions to script"
chmod +x detect-inactivity.sh
echo "2: Copying script to /usr/local/bin"
sudo cp -p detect-inactivity.sh /usr/local/bin
echo "3: Updating environment variable PATH"
export PATH=$PATH:/usr/local/bin
echo "4: Setting script to auto run at startup"
sudo sed -i -e '1idetect-inactivity.sh & || exit 1\n' /etc/rc.local 
detect-inactivity.sh &
echo "All set successfully! Script is Running in background."
