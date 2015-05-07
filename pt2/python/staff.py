from base import *

def insertStaff(records):
		
	sys.stdout.write('Updating Staff...')
	sys.stdout.flush()
	
	statement = 'INSERT INTO Staff VALUES(NULL, %s, %s, %s, (SELECT clubid FROM Clubs WHERE name = %s))'
	insertRecords(records, statement)
	
	print 'Done!'
		
	return
