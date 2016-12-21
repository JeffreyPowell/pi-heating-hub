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

  print("- - -")
  SCHED_START_HOUR, remainder = divmod(SCHED_START.seconds,3600)
  SCHED_START_MINUTE, sec = divmod(remainder, 60)
  
  SCHED_END_HOUR, remainder = divmod(SCHED_END.seconds,3600)
  SCHED_END_MINUTE, sec = divmod(remainder, 60)
  
  SCHED_START_STR = str(SCHED_START_HOUR)+":"+str(SCHED_START_MINUTE)
  SCHED_END_STR   = str(SCHED_END_HOUR) + ":"+str(SCHED_END_MINUTE)
  
  TIME_NOW = datetime.datetime.strptime(str(now.hour)+":"+str(now.minute), "%H:%M")
  TIME_START = datetime.datetime.strptime(SCHED_START_STR, "%H:%M")
  TIME_END = datetime.datetime.strptime(SCHED_END_STR, "%H:%M")
  MIN_TO_START = TIME_NOW - TIME_START
  MIN_TO_END   = TIME_END - TIME_NOW

  if ( MIN_TO_START.total_seconds() > 0 and MIN_TO_END.total_seconds() > 0 ):
    SCHED_TEST_TIME = True
  else:
    SCHED_TEST_TIME = False
    
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

  query = "SELECT * FROM sensors LEFT JOIN sched_sensor ON sensors.id=sched_sensor.sensor_id AND sched_sensor.sched_id="+SCHED_ID+";";

  cursorselect.execute(query)
  
  results_sensors =cursorselect.fetchall()
  cursorselect.close()
  for result in results_sensors:
    print( result )
 

  
#******************************************

#while($row = mysqli_fetch_assoc($result)) {
#    echo '<tr><td>'.$row["name"].'</td>';
#    $SENSOR_OPP = $row["opp"];
#    if ( $SENSOR_OPP == "" ) { $NA_SELECTED = 'selected'; }else{ $NA_SELECTED = ''; }
#    if ( $SENSOR_OPP == "<" ) { $LT_SELECTED = 'selected'; }else{ $LT_SELECTED = ''; }
#    if ( $SENSOR_OPP == "=" ) { $EQ_SELECTED = 'selected'; }else{ $EQ_SELECTED = ''; }
#    if ( $SENSOR_OPP == "!" ) { $NE_SELECTED = 'selected'; }else{ $NE_SELECTED = ''; }
#    if ( $SENSOR_OPP == ">" ) { $GT_SELECTED = 'selected'; }else{ $GT_SELECTED = ''; }
#    echo '<td><select name="sensor_opp">';
#    echo '<option value="na" '.$NA_SELECTED.' >(IS IGNORED)</option>';
#    echo '<option value="lt" '.$LT_SELECTED.' >IS LESS THAN</option>';
#    echo '<option value="eq" '.$EQ_SELECTED.' >IS EQUAL TO</option>';
#    echo '<option value="ne" '.$NE_SELECTED.' >IS NOT EQUAL TO</option>';
#    echo '<option value="gt" '.$GT_SELECTED.' >IS GREATER THAN</option>';
#    echo '</select></td>';
#    echo '<td><input type="text" name="sensor_value" value="'.$row["value"].'"></td></tr>';
#    }
#  *****************************************
  
  
  # Check modes

  # Check timers
  
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
