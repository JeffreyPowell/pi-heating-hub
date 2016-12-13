#!/usr/bin/env python

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

cursorread.execute(query)
  
results =cursorread.fetchall()
cursorread.close()
  
for i in results:
  sensor_ip = i[3]
  sensor_ref = i[1]
  sensor_id = i[0]
    
  sensor_url = "http://"+sensor_ip+":8080/value.php?id="+sensor_ref

  print sensor_url
    
  data = float( urllib2.urlopen(sensor_url).read() )
    
  print data
    
  cursorwrite = cnx.cursor()

  cursorwrite.execute("UPDATE sensors SET value='%s' WHERE id='%s';" % (data, sensor_id))

  cnx.commit()


cnx.close()
