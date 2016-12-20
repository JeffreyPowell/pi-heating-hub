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


cursorselect = cnx.cursor()

# Check schedule time and date

query = ("SELECT * FROM schedules WHERE enabled ='1';")
cursorselect.execute(query)
  
results_schedules =cursorselect.fetchall()
cursorselect.close()
  
for result in results_schedules:
  SCHED_ID = result[0]
  SCHED_START = result[1]
  SCHED_END = result[2]
  
  SCHED_MON = result[3]
  SCHED_TUE = result[4]
  SCHED_WED = result[5]
  SCHED_THU = result[6]
  SCHED_FRI = result[7]
  SCHED_SAT = result[8]
  SCHED_SUN = result[9]

  print( SCHED_ID )

# Check sensor values

# Check modes

# Check timers



cnx.commit()
cnx.close()
