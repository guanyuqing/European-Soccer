import codecs
import os
import re
import requests

from datetime import *

from base import *

def updateMatches():
	
	fetchMatches('../data/map/english.txt')
	fetchMatches('../data/map/italian.txt')
	fetchMatches('../data/map/german.txt')
	fetchMatches('../data/map/spanish.txt')
	fetchMatches('../data/map/french.txt')
	fetchMatches('../data/map/english-low.txt')
	
	return

def cleanMatches():
	
	if os.path.exists('../data/match'):
		for file in os.listdir('../data/match'):
			path = '../data/match/' + file
			os.remove(path)
	else:
		os.mkdir('../data/match')
	
	return

def fetchMatches(mapfilename):
		
	parts = mapfilename.rpartition('/')
	parts = parts[2].partition('.')
	shortname = parts[0]
	
	sys.stdout.write('Updating Matches from ' + shortname.title() + ' League...')
	sys.stdout.flush()
	
	dict = loadDict(mapfilename)
	thetype = int(dict['type'])
	
	prefix = dict['prefix']
	text = download(prefix, thetype)	
	
	matches = parse(text, dict)
	updateNewMatches(shortname, matches)
	
	print 'Done!'
	
	return
	
def updateNewMatches(shortname, matches):

	formattedMatches = [formatMatch(match) for match in matches]
	
	filepath = '../data/match/' + shortname + '.txt'
	
	datafile = codecs.open(filepath, 'a+', 'utf8')
	
	oldSet = set()
	[oldSet.add(line) for line in datafile.readlines()]
	
	datafile.seek(0, os.SEEK_CUR)
	
	newMatches = []
	
	for i in range(0, len(matches)):
		if not formattedMatches[i] in oldSet:
			datafile.write(formattedMatches[i])
			newMatches.append(matches[i])
	
	datafile.close()
		
	statement = 'INSERT INTO Matches VALUES(NULL, %s, %s, %s, %s, %s, 1)'
	insertRecords(newMatches, statement)
	
	try:
		[conn, cur] = getConn()
		
		statement = 'UPDATE Matches INNER JOIN Clubs on Clubs.clubid = Matches.home_team SET Matches.stadiumid = Clubs.stadiumid WHERE Matches.stadiumid = 1';
		cur.execute(statement)
		
		conn.commit()
		
		cur.close()
		conn.close()
		
	except MySQLdb.Error, e:
		print 'MySQL Error %d: %s' % (e.args[0], e.args[1])
		traceback.print_exc()
		
		sys.exit(1)
		
	return

	
def loadDict(mapfilename):

	dict = {}
		
	mapfile = open(mapfilename)
	lines = mapfile.readlines()
	mapfile.close()
	
	for line in lines:
		(key, val) = line.decode('utf8').split('=')
		dict[key] = val.replace('\n', '')

	return dict
	
def download(prefix, thetype):

	text = ''
	
	if thetype == 1:
		url = prefix + '-i.txt'
		r = requests.get(url)	
		text += r.text + '\n'
		
		url = prefix + '-ii.txt'
		r = requests.get(url)
		text += r.text + '\n'
	else:
		r = requests.get(prefix)
		text += r.text + '\n'
	
	return text

def formatMatch(match):
	
	output = str(match[0]) + ', '
	output += str(match[1]) + ', '
	
	output += str(match[2]) + ', '
	output += str(match[3]) + ', '
	
	output += match[4].strftime('%Y-%m-%d')
	output += '\n'
	
	return output

def parse(text, dict):
	
	thetype = int(dict['type'])
	matches = []
	
	tomorrow = date.today() + timedelta(days = 1);
	
	if thetype == 1:
		thedate = None
				
		monthFirst = True if dict['month_first'] == '1' else False
		
		regdate = dict['regdate']
		regmatch = dict['regmatch']
		
		lines = text.replace('\r', '').split('\n')
			
		for line in lines:
			matcher = re.match(regdate, line, re.M | re.I)
			
			if matcher:
				
				monText = matcher.group(1) if monthFirst else matcher.group(2)
				dayText = matcher.group(2) if monthFirst else matcher.group(1)
				
				mon = int(dict[monText])
				day = int(dayText)
							
				year = 2015 if mon < 8 else 2014
				
				thedate = date(year, mon, day)
				if thedate > tomorrow:
					break
				
				continue
			
			if not thedate:
				continue
			
			matcher = re.match(regmatch, line, re.M | re.I)
			
			if matcher:
				
				homeTeam = int(dict[matcher.group(1)])
				awayTeam = int(dict[matcher.group(4)])
				
				goalsHome = int(matcher.group(2))
				goalsAway = int(matcher.group(3))
				
				match = [homeTeam, awayTeam, goalsHome, goalsAway, thedate]
				matches.append(match)
	else:
		lines = text.replace('\r', '').split('\n')
		
		for line in lines:
			
			fields = line.split(',')
			[field.strip() for field in fields]
			
			if len(fields) < 64 or fields[0] == 'Div' or fields[2] == '' or fields[3] == '' or fields[4] == '' or fields[5] == '':
				continue
			
			thedate = datetime.strptime(fields[1], '%d/%m/%y').date()
			if thedate > tomorrow:
				break
			
			homeTeam = int(dict[fields[2]])
			awayTeam = int(dict[fields[3]])
			
			goalsHome = int(fields[4])
			goalsAway = int(fields[5])
				
			match = [homeTeam, awayTeam, goalsHome, goalsAway, thedate]
			matches.append(match)
	
	return matches
	