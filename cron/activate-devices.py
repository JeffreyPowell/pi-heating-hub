#!/usr/bin/env python

import MySQLdb
#import datetime
#import urllib2
#import os
import datetime
import RPi.GPIO as GPIO

try:
    import RPi.GPIO as GPIO
except RuntimeError:
    print("Error importing RPi.GPIO!")

servername = "localhost"
username = "pi"
password = "password"
dbname = "pi_heating_db"

cnx = MySQLdb.connect(host=servername, user=username, passwd=password, db=dbname)

cursorupdate = cnx.cursor()
query = ("UPDATE devices SET value = 0;")
cursorupdate.execute(query)
results_devices =cursorupdate.fetchall()
cursorupdate.close()

print("%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%")

cursorselect = cnx.cursor()
query = ("SELECT active, device_id  FROM schedules INNER JOIN sched_device ON schedules.id = sched_device.sched_id;")
cursorselect.execute(query)
results_devices =cursorselect.fetchall()
cursorselect.close()

for result in results_devices:
    print("* * * * * *")
    DEVICE_ACTIVE = bool( result[0] )
    DEVICE_ID = int( result[1] )
    print( DEVICE_ID, DEVICE_ACTIVE )
    
    if ( DEVICE_ACTIVE ):
        #print( DEVICE_ID, DEVICE_ACTIVE )
        cursorupdate = cnx.cursor()
        query = ("UPDATE devices SET value = 1 WHERE d_id = "+DEVICE_ID+";")
        cursorupdate.execute(query)
        results_devices =cursorupdate.fetchall()
        cursorupdate.close()

cursorselect = cnx.cursor()
query = ("SELECT * FROM devices;")
cursorselect.execute(query)
results_devices =cursorselect.fetchall()
cursorselect.close()

GPIO.setmode(GPIO.BOARD)
GPIO.setwarnings(False)

for result in results_devices:
    print("- - - - - - - -")
  
    DEVICE_PIN = int( result[2] )
    DEVICE_VALUE = int( result[3] )

    GPIO.setup(DEVICE_PIN, GPIO.OUT, initial=GPIO.LOW)
    
    GPIO.output(DEVICE_PIN, DEVICE_VALUE)
  
    print( DEVICE_PIN, DEVICE_VALUE )

cnx.close()
