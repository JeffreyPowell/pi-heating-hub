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
  print("***")
  SCHED_TEST_TIME     = False
  SCHED_TEST_DAY      = False
  SCHED_TEST_SENSORS  = False
  SCHED_TEST_MODES    = False
  SCHED_TEST_TIMERS   = False
  
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
  #print( now )
  #print( SCHED_START )
  #print( SCHED_END )
  #print( type(SCHED_END) )
  
  #print("---")
  SCHED_START_HOUR, remainder = divmod(SCHED_START.seconds,3600)
  SCHED_START_MINUTE, sec = divmod(remainder, 60)
  
  SCHED_END_HOUR, remainder = divmod(SCHED_END.seconds,3600)
  SCHED_END_MINUTE, sec = divmod(remainder, 60)
  
  #print( type(SCHED_START_MINUTE))
  #print( SCHED_START_MINUTE)
  
  #print("---")
  
  SCHED_START_STR = str(SCHED_START_HOUR)+":"+str(SCHED_START_MINUTE)
  SCHED_END_STR   = str(SCHED_END_HOUR) + ":"+str(SCHED_END_MINUTE)
  #print( SCHED_START_STR )
  
  TIME_NOW = datetime.datetime.strptime(str(now.hour)+":"+str(now.minute), "%H:%M")
  TIME_START = datetime.datetime.strptime(SCHED_START_STR, "%H:%M")
  TIME_END = datetime.datetime.strptime(SCHED_END_STR, "%H:%M")
  MIN_TO_START = TIME_NOW - TIME_START
  MIN_TO_END   = TIME_END - TIME_NOW


  #print( MIN_TO_START.total_seconds() )
  #print( MIN_TO_END.total_seconds() )
  
  #print( SCHED_TEST_TIME )
  
  if ( MIN_TO_START.total_seconds() > 0 and MIN_TO_END.total_seconds() > 0 ):
    SCHED_TEST_TIME = True
    
  print( SCHED_TEST_TIME )

  # Check senso values

  # Check modes

  # Check timers

  if ( SCHED_TEST_TIME and SCHED_TEST_DAY and SCHED_TEST_SENSORS and SCHED_TEST_MODES and SCHED_TEST_TIMERS  == True):
    print( "activate" )
  else:
    print( "deactivate" )
    

cnx.commit()
cnx.close()
