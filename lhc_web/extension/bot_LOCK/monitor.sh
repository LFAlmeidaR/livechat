#!/bin/sh

PINGRESPONSE=$(curl --max-time 10 -s "http://127.0.0.1:8080?ping=1&id=1&ct=1&sh=pje" 2>&1)

if test "${PINGRESPONSE}" != "pong"
then

kill -9 $(ps aux | grep "/var/www/chat/extension/bot/chatbot.py" | awk '{print $2}')
/usr/bin/nohup /opt/rh/rh-python36/root/usr/bin/python3.6 /var/www/chat/extension/bot/chatbot.py > /dev/null 2>&1

fi
