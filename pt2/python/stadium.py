from base import *

def insertStadium(records):
	
	sys.stdout.write('Updating Stadiums...')
	sys.stdout.flush()
	
	statement = 'INSERT INTO Stadiums VALUES(NULL, %s, %s, %s, %s)'
	insertRecords(records, statement)
	
	print 'Done!'
		
	return
	
