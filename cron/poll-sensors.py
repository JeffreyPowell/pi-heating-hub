#!/usr/bin/env python

import MySQLdb
import datetime
import urllib2
import os
  
servername = "localhost"
username = "pi"
password = "password"
dbname = "pi_heating_db"

t = datetime.datetime.now().strftime('%s')

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

  #print sensor_url
  
  try:
    data = float( urllib2.urlopen(sensor_url).read() )  
  except:
    data = 'na'
    
  print data
  print sensor_id
  
  if( data != 'na' ):
    print "database"
    #print sensor_id
    
    sql = "UPDATE sensors SET value='"+str(data)+"' WHERE id='"+sensor_id+"';"
    
    print sql
    
    cursorwrite = cnx.cursor()
    
    #cursorwrite.execute( sql )
    
    try:
      cursorwrite.execute( sql )
      #rows = cur.fetchall()
    except MySQLdb.Error, e:
      try:
        print "MySQL Error [%d]: %s" % (e.args[0], e.args[1])
      except IndexError:
        print "MySQL Error: %s" % str(e)
    
    cnx.commit
    print "database done"
  
  filename = '/home/pi/pi-heating-hub/data/s-'+str(sensor_id)+'.rrd'
  
  print filename

  if( not os.path.exists( filename ) ):
      print ( os.path.exists( filename ))
      os.system('/usr/bin/rrdtool create '+filename+' --step 60 \
      --start now \
      DS:data:GAUGE:120:U:U \
      RRA:MIN:0.5:1:10080 \
      RRA:MIN:0.5:5:51840 \
      RRA:MIN:0.5:60:8760 \
      RRA:AVERAGE:0.5:1:10080 \
      RRA:AVERAGE:0.5:5:51840 \
      RRA:AVERAGE:0.5:60:8760 \
      RRA:MAX:0.5:1:10080 \
      RRA:MAX:0.5:5:51840 \
      RRA:MAX:0.5:60:8760')

  if( data != 'na' ):
    print"rrd"
    os.system('/usr/bin/rrdtool update '+filename+" "+str(t)+':'+str(data))


cnx.close()
