#!/usr/bin/env python

import MySQLdb

import datetime
  
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

now = datetime.datetime.now()
DOW = datetime.datetime.today().weekday()

cnx = MySQLdb.connect(host=servername, user=username, passwd=password, db=dbname)


cursorselect = cnx.cursor()

# Check schedule time and date

query = ("SELECT * FROM schedules WHERE enabled ='1';")
cursorselect.execute(query)
  
results_schedules =cursorselect.fetchall()
cursorselect.close()
  
for result in results_schedules:
  print("* * * * * *")
  SCHED_TEST_TIME     = False
  SCHED_TEST_DAY      = False
  SCHED_TEST_SENSORS  = False
  SCHED_TEST_MODES    = False
  SCHED_TEST_TIMERS   = False
  
  SCHED_ID = str(result[0])
  SCHED_NAME = str(result[1])
  SCHED_START = result[2]
  SCHED_END = result[3]
  
  SCHED_MON = result[4]
  SCHED_TUE = result[5]
  SCHED_WED = result[6]
  SCHED_THU = result[7]
  SCHED_FRI = result[8]
  SCHED_SAT = result[9]
  SCHED_SUN = result[10]

  print( '######################################' )
  print( SCHED_ID, SCHED_NAME )
  print( '######################################' )
  
  SCHED_START_HOUR, remainder = divmod(SCHED_START.seconds,3600)
  SCHED_START_MINUTE, sec = divmod(remainder, 60)
  
  SCHED_END_HOUR, remainder = divmod(SCHED_END.seconds,3600)
  SCHED_END_MINUTE, sec = divmod(remainder, 60)
  
  SCHED_START_STR = str(SCHED_START_HOUR)+":"+str(SCHED_START_MINUTE)
  SCHED_END_STR   = str(SCHED_END_HOUR) + ":"+str(SCHED_END_MINUTE)
  SCHED_NOW_STR   = str(now.hour)+":"+str(now.minute)
  
  TIME_NOW = datetime.datetime.strptime(SCHED_NOW_STR, "%H:%M")
  TIME_START = datetime.datetime.strptime(SCHED_START_STR, "%H:%M")
  TIME_END = datetime.datetime.strptime(SCHED_END_STR, "%H:%M")
  MIN_TO_START = TIME_NOW - TIME_START
  MIN_TO_END   = TIME_END - TIME_NOW

  if ( MIN_TO_START.total_seconds() > 0 and MIN_TO_END.total_seconds() > 0 ):
    SCHED_TEST_TIME = True
  else:
    SCHED_TEST_TIME = False
    
  print( SCHED_START_STR, SCHED_NOW_STR, SCHED_END_STR, SCHED_TEST_TIME )
    
  if (  SCHED_MON and DOW == 0 ):
    SCHED_TEST_DAY = True
  elif( SCHED_TUE and DOW == 1 ):
    SCHED_TEST_DAY = True
  elif( SCHED_WED and DOW == 2 ):
    SCHED_TEST_DAY = True
  elif( SCHED_THU and DOW == 3 ):
    SCHED_TEST_DAY = True
  elif( SCHED_FRI and DOW == 4 ):
    SCHED_TEST_DAY = True
  elif( SCHED_SAT and DOW == 5 ):
    SCHED_TEST_DAY = True
  elif( SCHED_SUN and DOW == 6 ):
    SCHED_TEST_DAY = True
  else:
    SCHED_TEST_DAY = False
    
  # Check sensor values
  
  cursorselect = cnx.cursor()
  query = "SELECT * FROM sensors INNER JOIN sched_sensor ON sensors.id=sched_sensor.sensor_id AND sched_sensor.sched_id="+SCHED_ID+";";
  cursorselect.execute(query)
  results_sensors =cursorselect.fetchall()
  cursorselect.close()
  
  SCHED_TEST_SENSORS = True
  
  for result in results_sensors:
    #print( result )
    if ( result[4] != None ):
    
      SENSOR_VALUE= float(result[4])
      SENSOR_TEST = str(result[9])
      TEST_VALUE = float(result[10])

      if (  SENSOR_TEST == '<' and SENSOR_VALUE < TEST_VALUE ):
        TEST = True
      elif( SENSOR_TEST == '=' and SENSOR_VALUE == TEST_VALUE ):
        TEST = True
      elif( SENSOR_TEST == '!' and SENSOR_VALUE != TEST_VALUE ):
        TEST = True
      elif( SENSOR_TEST == '>' and SENSOR_VALUE > TEST_VALUE ):
        TEST = True
      else:
        TEST = False

      if TEST == False:
        SCHED_TEST_SENSORS = False
  
    #print( SENSOR_VALUE, SENSOR_TEST, TEST_VALUE, SCHED_TEST_SENSORS )

  # Check modes

  cursorselect = cnx.cursor()
  query = "SELECT * FROM modes INNER JOIN sched_mode ON modes.id=sched_mode.mode_id AND sched_mode.sched_id='"+SCHED_ID+"';"
  cursorselect.execute(query)
  results_modes =cursorselect.fetchall()
  cursorselect.close()
  
  print( '----------------------------' )
  
  print( results_modes )
  
  SCHED_TEST_MODES = True
   
  for result in results_modes:
    #print( result )
    MODE_VALUE= bool(result[2])
    MODE_TEST = str(result[6])
    TEST_VALUE = bool(result[7])
    
    if (  MODE_VALUE == TEST_VALUE ):
      TEST = True
      print('[1]')
    else:
      TEST = False
      print('[3]')
      
    if TEST == False:
      SCHED_TEST_MODES = False
  
    print( MODE_VALUE, MODE_TEST, TEST_VALUE, SCHED_TEST_MODES )  
 
    print( '- - - - - - - - - - - - - - ' )
    # Check timers
  
  cursorselect = cnx.cursor()
  query = "SELECT timers.value, sched_timer.value FROM timers INNER JOIN sched_timer ON timers.id = sched_timer.timer_id AND sched_timer.sched_id ="+SCHED_ID+";"
  cursorselect.execute(query)
  results_timers =cursorselect.fetchall()
  cursorselect.close()
  
  #print( results_timers )
  
  SCHED_TEST_TIMERS = True
   
  for result in results_timers:
    print( result )
    TIMER_VALUE= result[0]

    TEST_VALUE = result[1]
    
    if (  TEST_VALUE == True and TIMER_VALUE > 0 ):
      TEST = True
    elif ( TEST_VALUE == False and TIMER_VALUE == 0 ): ## Thanks to Alan Riley for spotting this.
      TEST = True
    else:
      TEST = False
      
    if TEST == False:
      SCHED_TEST_TIMERS = False
  
    print( TIMER_VALUE, TEST_VALUE, SCHED_TEST_TIMERS ) 
  
  
  

  print( SCHED_TEST_TIME, SCHED_TEST_DAY, SCHED_TEST_SENSORS, SCHED_TEST_MODES, SCHED_TEST_TIMERS )

  if ( SCHED_TEST_TIME and SCHED_TEST_DAY and SCHED_TEST_SENSORS and SCHED_TEST_MODES and SCHED_TEST_TIMERS  == True):
    print( "activate" )
    query = ("UPDATE schedules SET active = 1 WHERE id ='"+SCHED_ID+"';")
  else:
    print( "deactivate" )
    query = ("UPDATE schedules SET active = 0 WHERE id ='"+SCHED_ID+"';")
    
  cursorupdate = cnx.cursor()
  cursorupdate.execute(query)
  
  results =cursorupdate.fetchall()
  cursorupdate.close()
  cnx.commit()
  
cnx.close()
