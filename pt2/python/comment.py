from base import *

def insertComment(records):
	
	sys.stdout.write('Updating Comments...')
	sys.stdout.flush()
	
	for record in records:
		if record[1] == 'null':
			record[1] = None
		if record[2] == 'null':
			record[2] = None
		record[3] = record[3].replace('##', ',')
		
	statement = 'INSERT INTO Comments VALUES(NULL, (SELECT pid FROM Players WHERE name = %s), %s, %s, %s)'
	insertRecords(records, statement)
	
	print 'Done!'
		
	return
	