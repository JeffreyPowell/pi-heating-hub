#!/usr/bin/env python

import MySQLdb
import datetime
import urllib2
import os

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
cnx.autocommit(True)
cursorread = cnx.cursor()
query = ("SELECT id, ref, ip FROM sensors")
cursorread.execute(query)
results =cursorread.fetchall()
cursorread.close()
cnx.close()
  
for i in results:
  sensor_id = i[0]
  sensor_ref = i[1]
  sensor_ip = i[2]
  
    
  sensor_url = "http://"+sensor_ip+":8080/value.php?id="+sensor_ref

  print sensor_url
  
  try:
    data = float( urllib2.urlopen(sensor_url).read() )  
  except:
    data = 'NULL'
    
  print data
  print sensor_id
   
  sql = "UPDATE sensors SET value="+str(data)+" WHERE id='"+str(sensor_id)+"';"

  print sql
       
  #cursorwrite.execute( sql )
    
  try:
    cnx = MySQLdb.connect(host=servername, user=username, passwd=password, db=dbname)
    cnx.autocommit(True)
    cursorwrite = cnx.cursor()
    cursorwrite.execute( sql )
      
    print("affected rows = {}".format(cursorwrite.rowcount))
      
    cursorwrite.close()
    cnx.close()

    #rows = cur.fetchall()
  except MySQLdb.Error, e:
    try:
      print "MySQL Error [%d]: %s" % (e.args[0], e.args[1])
    except IndexError:
      print "MySQL Error: %s" % str(e)
    

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

  if( data != 'NULL' ):
    print"rrd"
    os.system('/usr/bin/rrdtool update '+filename+" "+str(t)+':'+str(data))

