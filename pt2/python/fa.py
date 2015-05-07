from base import *

def insertFa(records):
	
	sys.stdout.write('Updating Football Associations...')
	sys.stdout.flush()
	
	statement = 'INSERT INTO Football_associations VALUES(NULL, %s, %s, %s)'
	insertRecords(records, statement)
	
	print 'Done!'
	
	return
	

	