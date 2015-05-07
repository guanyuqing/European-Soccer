import copy
import sys
import traceback 

from base import *

def insertLeague(records):
	
	sys.stdout.write('Updating Leagues...')
	sys.stdout.flush()
	
	statement = 'INSERT INTO Leagues VALUES(NULL, %s, %s, %s, (SELECT fid FROM Football_associations WHERE name = %s), NULL) /* %s */'
	insertRecords(records, statement)
	
	print 'Done!'
		
	return
	
def setLastChampion(records):
	
	sys.stdout.write('Updating Last Champions...')
	sys.stdout.flush()
	
	try:
		[conn, cur] = getConn()
		
		statement = 'UPDATE Leagues SET last_champion = (SELECT clubid FROM Clubs WHERE name = %s) WHERE name = %s'
		
		for record in records:
			cur.execute(statement, [record[4], record[0]])
		
		conn.commit()
		
		cur.close()
		conn.close()
		
	except MySQLdb.Error, e:
		print 'MySQL Error %d: %s' % (e.args[0], e.args[1])
		traceback.print_exc()
		
		sys.exit(1)
		
	print 'Done!'
		
	return
	
