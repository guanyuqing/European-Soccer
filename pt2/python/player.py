from base import *

def insertPlayer(records):
	
	sys.stdout.write('Updating Players...')
	sys.stdout.flush()
	
	countryDict = mapCountryName()
	
	for record in records:
		record[1] = countryDict[record[1]]
	
	statement = 'INSERT INTO Players VALUES(NULL, %s, %s, %s, %s, (SELECT clubid FROM Clubs WHERE name = %s))'
	insertRecords(records, statement)
	
	print 'Done!'
		
	return
