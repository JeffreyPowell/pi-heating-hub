#!/usr/bin/env python

#import sys
#import os
#import logging
#import pif
import MySQLdb
import datetime
import urllib2

  
servername = "localhost"
username = "pi"
password = "password"
dbname = "pi_heating_db"

cnx = MySQLdb.connect(host=servername, user=username, passwd=password, db=dbname)
cursorread = cnx.cursor()

query = ("SELECT * FROM sensors")

cursor.execute(query)
  
results =cursor.fetchall()
cursorread.close()
  
for i in results:
  sensor_ip = i[3]
  sensor_ref = i[1]
  sensor_id = i[0]
    
  sensor_url = "http://"+sensor_ip+":8080/value.php?id="+sensor_ref

  print sensor_url
    
  data = urllib2.urlopen(sensor_url).read()
    
  print data
    
  cursorwrite = cnx.cursor()

  cursowrite.execute("UPDATE sensors SET value=%s WHERE ='%s' " % (data, sensor_id))

  cnx.commit()


cnx.close()
