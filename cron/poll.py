#!/usr/bin/env python

#import sys
#import os
#import logging
#import pif
import MySQLdb

# https://dev.mysql.com/doc/connector-python/en/connector-python-example-connecting.html


# GLOBALS
MYSQL_USERNAME="pi"
MYSQL_PASSWORD="PASSWORD"


 
#
# check locally if IP has changed
#
def check_ip_file(public_ip):
  if os.path.exists(IPFILE):
    # Open a file
    fo = open(IPFILE, "r")
    old_ip = fo.read(50)
    fo.close()
    #print "Read String is : ", str
    # Close opend file
    if old_ip == public_ip:
      print "ip is the same.. not doing anything"
      return 1
  # return if no file exists, or the IP is new
  return
 
def poll_all_sensors():
  import datetime
  import mysql.connector

  cnx = MySQLdb.connect(host="localhost", user="pi", passwd="password", db="pi-heating-hub")
  cursor = cnx.cursor()

  query = ("SELECT first_name, last_name, hire_date FROM employees "
          "WHERE hire_date BETWEEN %s AND %s")

  hire_start = datetime.date(1999, 1, 1)
  hire_end = datetime.date(1999, 12, 31)

  cursor.execute(query, (hire_start, hire_end))

  for (first_name, last_name, hire_date) in cursor:
    print("{}, {} was hired on {:%d %b %Y}".format(last_name, first_name, hire_date))

  cursor.close()
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

db = MySQLdb.connect(host="localhost", user="pi", passwd="password", db="pi-heating-hub")

poll_all_sensors(db)
update_all_timers(db)

action_all_schedules(db)
