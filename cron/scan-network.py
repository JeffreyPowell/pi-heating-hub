#!/usr/bin/env python

import MySQLdb
import datetime
import urllib2
import os
import subprocess
  
try:
    from configparser import ConfigParser
except ImportError:
    from ConfigParser import ConfigParser

config = ConfigParser()
config.read('/home/pi/pi-heating-hub/config/config.ini')

servername = config.get('db', 'server')
username = config.get('db', 'user')
password = config.get('db', 'password')
dbname = config.get('db', 'database')

t = datetime.datetime.now().strftime('%s')

cnx = MySQLdb.connect(host=servername, user=username, passwd=password, db=dbname)
cursorread = cnx.cursor()

query = ("SELECT * FROM network")

cursorread.execute(query)
  
results =cursorread.fetchall()
cursorread.close()

network_data = subprocess.check_output('sudo nmap -sP 192.168.0.0/24', shell=True)

print network_data

for i in results:
  network_id = i[0]
  network_mac = i[1]

  print network_mac
    
  if network_mac in network_data:
    data = 1
  else:
    data = 0
  
  print data
    
  cursorwrite = cnx.cursor()

  cursorwrite.execute("UPDATE network SET value='%s' WHERE id='%s';" % (data, network_id))

  cnx.commit()

cnx.close()
