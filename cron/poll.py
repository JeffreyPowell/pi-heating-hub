#!/usr/bin/env python

#import sys
#import os
#import logging
#import pif
import MySQLdb
import datetime
import urllib2
#import mysql.connector
 
# https://dev.mysql.com/doc/connector-python/en/connector-python-example-connecting.html


# GLOBALS
#MYSQL_USERNAME="pi"
#MYSQL_PASSWORD="PASSWORD"


 
#
# check locally if IP has changed
#

 
def poll_all_sensors():
  #import datetime
  #import mysql.connector
  #import MySQLdb
  
  servername = "localhost"
  username = "pi"
  password = "password"
  dbname = "pi_heating_db"

  cnx = MySQLdb.connect(host=servername, user=username, passwd=password, db=dbname)
  cursor = cnx.cursor()

  query = ("SELECT * FROM sensors")

  cursor.execute(query)
  
  results =cursor.fetchall()
  cursor.close()
  
  for i in results:
    sensor_ip = i[3]
    sensor_ref = i[2]
    
    sensor_url = "http://"+sensor_ip+":8080/value.php?id="+sensor_ref

    print sensor_url
    
    data = urllib2.urlopen(sensor_url).read()
    
    print data


  cnx.close()
  
  # read list of sensors
  # loop through sensors write values to db
  # 

  return
 
def update_all_timers():
  return

def action_all_schedules():
  return

### BEGIN MAIN PROCEDURE ###

#db = MySQLdb.connect(host="localhost", user="pi", passwd="password", db="pi-heating-hub")

poll_all_sensors()

update_all_timers()

action_all_schedules()
