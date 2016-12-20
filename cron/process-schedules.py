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
cursorupdate = cnx.cursor()

# Check schedule time and date

query = ("SELECT * FROM schedules")
cursorselect.execute(query)



# Check sensor values

# Check modes

# Check timers


cursorupdate.close()
cnx.commit()
cnx.close()
