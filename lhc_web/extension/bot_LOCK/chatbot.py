#!/usr/bin/env python
import logging
from http.server import BaseHTTPRequestHandler, HTTPServer
from os import curdir, sep

# Settings application
import settings as s
import lhcHandler as httpHandler
import lhcChatBot as lhcBot

# set utf-8 encoding
import sys
import imp
imp.reload(sys)


#import logging
#logger = logging.getLogger()

# Enable info level logging
#logging.basicConfig(level=logging.CRITICAL)


try:
	#Create a web server and define the handler to manage the
	#incoming request	
	handler = httpHandler.lhcHandler
	handler.settings = s.app
	handler.bot = lhcBot.lhcChatBot()
	handler.bot.settings = s.database
	
	server = HTTPServer(('', s.http['PORT_NUMBER']), handler)
	print(('Started httpserver on port ' , s.http['PORT_NUMBER']))
	
	#Wait forever for incoming htto requests
	server.serve_forever()

except KeyboardInterrupt:
	print ('^C received, shutting down the web server')
	server.socket.close()

