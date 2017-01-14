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

# Set all devices as inactive
cursorupdate = cnx.cursor()
query = ("UPDATE devices SET value = 0;")
cursorupdate.execute(query)
results_devices =cursorupdate.fetchall()
cursorupdate.close()

print("%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%")

# Just set active devices that have active schedules
cursorselect = cnx.cursor()
query = ("SELECT active, device_id  FROM schedules INNER JOIN sched_device ON schedules.id = sched_device.sched_id WHERE active = 1;")
cursorselect.execute(query)
results_devices =cursorselect.fetchall()
cursorselect.close()

for result in results_devices:
    #print("* * * * * *")
    DEVICE_ACTIVE = bool( result[0] )
    DEVICE_ID = str( result[1] )
    print( DEVICE_ID, DEVICE_ACTIVE )
    
    cursorupdate = cnx.cursor()
    query = ("UPDATE devices SET value = 1 WHERE d_id = "+DEVICE_ID+";")
    cursorupdate.execute(query)
    results_devices =cursorupdate.fetchall()
    cursorupdate.close()

# Pysically turn ON/OFF GPIO pin for devices

cursorselect = cnx.cursor()
query = ("SELECT name, pin, active_level, value FROM devices WHERE active_level IS NOT NULL;")
cursorselect.execute(query)
results_devices =cursorselect.fetchall()
cursorselect.close()

GPIO.setmode(GPIO.BOARD)
GPIO.setwarnings(False)

for result in results_devices:
    print("- - - - - - - -")
    DEVICE_NAME = int( result[0] )
    DEVICE_PIN = int( result[1] )
    DEVICE_ACTIVE_LEVEL = bool( result[2] )
    DEVICE_VALUE = bool( result[3] )

    GPIO.setup(DEVICE_PIN, GPIO.OUT, initial=GPIO.LOW)
    
    GPIO.output(DEVICE_PIN, ~(DEVICE_ACTIVE_LEVEL ^ DEVICE_VALUE) )
  
    print( DEVICE_NAME, DEVICE_PIN, DEVICE_ACTIVE_LEVEL, DEVICE_VALUE, ~(DEVICE_ACTIVE_LEVEL ^ DEVICE_VALUE) )

cnx.close()
