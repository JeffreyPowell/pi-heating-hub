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

GPIO.setmode(GPIO.BOARD)

cnx = MySQLdb.connect(host=servername, user=username, passwd=password, db=dbname)

cursorselect = cnx.cursor()

query = ("SELECT * FROM devices;")
cursorselect.execute(query)
  
results_devices =cursorselect.fetchall()
cursorselect.close()
  
for result in results_devices:
    #print("* * * * * *")
  
    DEVICE_PIN = int( result[2] )
    DEVICE_VALUE = int( result[3] )

    GPIO.setup(DEVICE_PIN, GPIO.OUT, initial=GPIO.LOW)
    
    GPIO.output(DEVICE_PIN, DEVICE_VALUE)
  
    #print( DEVICE_PIN, DEVICE_VALUE )

cnx.close()
