import MySQLdb
import os
import sys
import traceback 

host = {'local': '', 'remote': ''}
user = {'local': '', 'remote': ''}
pwd = {'local': '', 'remote': ''}

binary = r'"C:\Program Files (x86)\MySQL\MySQL Server 5.6\bin\mysql.exe"'
create_tables = r'..\data\create_tables.sql'

def init(_mode):
	
	global mode
	mode = _mode
	
	print 'Initializing the ' + mode + ' server...'
	os.system(binary + ' -h ' + host[mode] + ' -u' + user[mode] + ' -p' + pwd[mode] + ' --default-character-set=utf8 < ' + create_tables)
	print 'Initialization completed!'
	
	return

def getConn():
	
	conn = MySQLdb.connect(host[mode], user[mode], pwd[mode], 'cs4111', charset='utf8', init_command='SET NAMES UTF8')
	cur = conn.cursor()
	
	return [conn, cur]

def csv2records(filename):
	
	infile = open(filename)
	lines = infile.readlines()
	infile.close()
	
	records = [line.decode('utf8').split(',') for line in lines]
	
	for i, record in enumerate(records):
		records[i] = [field.strip() for field in record]
		
	return records
	
def insertRecords(records, statement):
	
	try:
		[conn, cur] = getConn()
		
		cur.executemany(statement, records)
		
		conn.commit()
		
		cur.close()
		conn.close()
		
	except MySQLdb.Error, e:
		print 'MySQL Error %d: %s' % (e.args[0], e.args[1])
		traceback.print_exc()
		
		sys.exit(1)
		
	return
	
def mapCountryName():

	filename = '../data/map/country.txt'
	dict = {}
	
	infile = open(filename)
	lines = infile.readlines()
	infile.close()
	
	for line in lines:
		(key, val) = line.decode('utf8').split('=')
		dict[key] = val.replace('\n', '')
	
	return dict
