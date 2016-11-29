
https://dev.mysql.com/doc/connector-python/en/connector-python-example-connecting.html

#SQL Connection Test
import MySQLdb
db = MySQLdb.connect(host="localhost", user="pi", passwd="password", db="pi-heating-hub")

cur = db.cursor()

