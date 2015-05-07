import shutil

from base import *
from fa import *
from stadium import *
from league import *
from club import *
from player import *
from staff import *
from match import *
from comment import *

init('remote')

fas = csv2records('../data/csv/fa.csv')
insertFa(fas)

stadiums = csv2records('../data/csv/stadium.csv')
insertStadium(stadiums)

leagues = csv2records('../data/csv/league.csv')
insertLeague(leagues)

clubs = csv2records('../data/csv/club.csv')
insertClub(clubs)

setLastChampion(leagues)

players = csv2records('../data/csv/player.csv')
insertPlayer(players);

setCaptains(clubs)

staff = csv2records('../data/csv/staff.csv')
insertStaff(staff);

cleanMatches()
updateMatches()

comments = csv2records('../data/csv/comment.csv')
insertComment(comments);

