#!/usr/bin/env python

import MySQLdb
  
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

cnx = MySQLdb.connect(host=servername, user=username, passwd=password, db=dbname)
cursorupdate = cnx.cursor()

query = ("UPDATE `timers` set value = value-1 WHERE value > 0;")
cursorupdate.execute(query)

cursorupdate.close()
cnx.commit()
cnx.close()
