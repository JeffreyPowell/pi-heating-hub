#!/usr/bin/env python

import MySQLdb
#import datetime
#import urllib2
#import os
import datetime
  
servername = "localhost"
username = "pi"
password = "password"
dbname = "pi_heating_db"

now = datetime.datetime.now()

cnx = MySQLdb.connect(host=servername, user=username, passwd=password, db=dbname)


cursorselect = cnx.cursor()

# Check schedule time and date

query = ("SELECT * FROM schedules WHERE enabled ='1';")
cursorselect.execute(query)
  
results_schedules =cursorselect.fetchall()
cursorselect.close()
  
for result in results_schedules:
  SCHED_ID = result[0]
  SCHED_START = result[2]
  SCHED_END = result[3]
  
  SCHED_MON = result[4]
  SCHED_TUE = result[5]
  SCHED_WED = result[6]
  SCHED_THU = result[7]
  SCHED_FRI = result[8]
  SCHED_SAT = result[9]
  SCHED_SUN = result[10]
  
  print( SCHED_ID )
  print( now )
  print( SCHED_START )
  print( SCHED_END )
  print( type(SCHED_END) )
  
  timeA = datetime.datetime.strptime(str(now.hour)+":"+str(now.minute), "%H:%M")
  timeB = datetime.datetime.strptime(str(SCHED_START.seconds//3600)+":"+str(SCHED_START.seconds//60), "%H:%M")
  #newTime = timeA - timeB


  print( timeA )
  print( timeB )

# Check sensor values

# Check modes

# Check timers



cnx.commit()
cnx.close()
