from base import *

def insertClub(records):
	
	sys.stdout.write('Updating Clubs...')
	sys.stdout.flush()
	
	for record in records:
		if record[2] == 'null':
			record[2] = None
	
	statement = 'INSERT INTO Clubs VALUES(NULL, %s, %s, %s, 0, 0, 0, 0, 0, (SELECT lid FROM Leagues WHERE name = %s), (SELECT stadiumid FROM Stadiums WHERE name = %s), NULL) /* %s */'
	insertRecords(records, statement)
	
	try:
		[conn, cur] = getConn()
		
		statement = 'UPDATE Stadiums SET name = \'Stadio Olimpico\' WHERE name like \'Stadio Olimpico%\'';
		cur.execute(statement)
		
		conn.commit()
		
		cur.close()
		conn.close()
		
	except MySQLdb.Error, e:
		print 'MySQL Error %d: %s' % (e.args[0], e.args[1])
		traceback.print_exc()
		
		sys.exit(1)
	
	print 'Done!'
	
	return
	
def setCaptains(records):
	
	sys.stdout.write('Updating Captains...')
	sys.stdout.flush()
	
	try:
		[conn, cur] = getConn()
		
		statement = 'UPDATE Clubs INNER JOIN Players ON Clubs.clubid = Players.clubid SET captain = Players.pid WHERE Players.name = %s and Clubs.name = %s'
		
		for record in records:
			cur.execute(statement, [record[5], record[0]])
		
		conn.commit()
		
		cur.close()
		conn.close()
		
	except MySQLdb.Error, e:
		print 'MySQL Error %d: %s' % (e.args[0], e.args[1])
		traceback.print_exc()
		
		sys.exit(1)
		
	print 'Done!'
		
	return
	