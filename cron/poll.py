#!/usr/bin/env python

#import sys
#import os
#import logging
#import pif
import MySQLdb

# https://dev.mysql.com/doc/connector-python/en/connector-python-example-connecting.html


# GLOBALS
MYSQL_USERNAME="USERNAME"
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
 

 
### BEGIN MAIN PROCEDURE

poll_all_sensors()
public_ip = pif.get_public_ip()
 
if check_ip_file(public_ip) != 1:
  update_dns(public_ip)
  
  #SQL Connection Test
db = MySQLdb.connect(host="localhost", user="pi", passwd="password", db="pi-heating-hub")
cur = db.cursor()
  
