#!/usr/bin/env python

import MySQLdb
#import datetime
#import urllib2
#import os
  
servername = "localhost"
username = "pi"
password = "password"
dbname = "pi_heating_db"

cnx = MySQLdb.connect(host=servername, user=username, passwd=password, db=dbname)
cursorread = cnx.cursor()

query = ("UPDATE `timers` set value = value -1 WHERE value >0")
cursorupdate.execute(query)

cursorupdate.close()
cnx.commit()
cnx.close()
