# In order to enable stored procedures and triggers on Amazon RDS,
# please set the parameter log_bin_trust_function_creators = 1

# Our CHECK Constraints are defined in the end of this file,
# after all 'CREATE TABLE' statements, triggers and stored procedures.

# Create database and set encoding

DROP DATABASE IF EXISTS cs4111;
CREATE DATABASE cs4111 CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE cs4111;

# Create tables and primary / foreign key constraints

# Football associations

CREATE TABLE Football_associations(
	fid SMALLINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name NVARCHAR(50) NOT NULL,
	country NVARCHAR(50) NOT NULL,
	chairperson NVARCHAR(50),
	UNIQUE (country)
) ENGINE = InnoDB  DEFAULT CHARSET = UTF8;

# Leagues without the foreign key constraint for last_champion
# This constraint will be added after the creation of the table of clubs.

CREATE TABLE Leagues(
	lid SMALLINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name NVARCHAR(50) NOT NULL,
	no_of_upgrade TINYINT NOT NULL,
	no_of_downgrade TINYINT NOT NULL,
	fid SMALLINT NOT NULL,
	last_champion SMALLINT,
	UNIQUE (name, fid),
	FOREIGN KEY (fid) REFERENCES Football_associations (fid) ON DELETE NO ACTION
) ENGINE = InnoDB  DEFAULT CHARSET = UTF8;

# Stadiums

CREATE TABLE Stadiums(
	stadiumid SMALLINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name NVARCHAR(50) NOT NULL,
	city NVARCHAR(50) NOT NULL,
	country NVARCHAR(50) NOT NULL,
	capacity INT NOT NULL,
	UNIQUE (name, city)
) ENGINE = InnoDB  DEFAULT CHARSET = UTF8;

# Clubs without the foreign key constraint for captain
# This constraint will be added after the creation of the table of players.

CREATE TABLE Clubs(
	clubid SMALLINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name NVARCHAR(50) NOT NULL,
	year_founded SMALLINT NOT NULL,
	european_qualification NVARCHAR(50),
	goals_for SMALLINT NOT NULL,
	goals_against SMALLINT NOT NULL,
	win TINYINT NOT NULL,
	draw TINYINT NOT NULL,
	lose TINYINT NOT NULL,
	lid SMALLINT NOT NULL,
	stadiumid SMALLINT,
	captain SMALLINT,
	UNIQUE (name, lid),
	FOREIGN KEY (lid) REFERENCES Leagues (lid) ON DELETE NO ACTION,
	FOREIGN KEY (stadiumid) REFERENCES Stadiums (stadiumid) ON DELETE SET NULL
) ENGINE = InnoDB  DEFAULT CHARSET = UTF8;

# Foreign key constraint of the last_champion field in leagues

ALTER TABLE Leagues
ADD FOREIGN KEY (last_champion)
REFERENCES Clubs (clubid) ON DELETE SET NULL;

# Players

CREATE TABLE Players(
	pid SMALLINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name NVARCHAR(50) NOT NULL,
	nationality NVARCHAR(50) NOT NULL,
	no TINYINT NOT NULL,
	position NVARCHAR(3) NOT NULL,
	clubid SMALLINT NOT NULL,
	UNIQUE (no, clubid),
	FOREIGN KEY (clubid) REFERENCES Clubs (clubid) ON DELETE NO ACTION
) ENGINE = InnoDB  DEFAULT CHARSET = UTF8;

# Foreign key constraint of the captain field in players

ALTER TABLE Clubs
ADD FOREIGN KEY (captain)
REFERENCES Players (pid) ON DELETE SET NULL;

# Staff

CREATE TABLE Staff(
	staffid SMALLINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name NVARCHAR(50) NOT NULL,
	nationality NVARCHAR(50) NOT NULL,
	type NVARCHAR(50) NOT NULL,
	clubid SMALLINT NOT NULL,
	FOREIGN KEY (clubid) REFERENCES Clubs (clubid) ON DELETE NO ACTION
) ENGINE = InnoDB  DEFAULT CHARSET = UTF8;

# Matches

CREATE TABLE Matches(
	mid SMALLINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	home_team SMALLINT NOT NULL,
	away_team SMALLINT NOT NULL,
	goals_home SMALLINT NOT NULL,
	goals_away SMALLINT NOT NULL,
	date DATE NOT NULL, 
	stadiumid SMALLINT NOT NULL,
	FOREIGN KEY (home_team) REFERENCES Clubs (clubid) ON DELETE NO ACTION,
	FOREIGN KEY (away_team) REFERENCES Clubs (clubid) ON DELETE NO ACTION,
	FOREIGN KEY (stadiumid) REFERENCES Stadiums (stadiumid) ON DELETE NO ACTION
) ENGINE = InnoDB  DEFAULT CHARSET = UTF8;

# Comments
# We transformed the comments from weak entites to strong entites,
# as we want to make comment ids increase automatically.
# In order to implement this function, MySQL requires us to set commentid
# to be an independent primary key, so we do not use (pid, commentid) as
# the primary key.

CREATE TABLE Comments(
	commentid SMALLINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	pid SMALLINT NOT NULL,
	commenter_name NVARCHAR(50),
	rating TINYINT,
	content NVARCHAR(1000) NOT NULL,
	FOREIGN KEY (pid) REFERENCES Players (pid) ON DELETE CASCADE
) ENGINE = InnoDB  DEFAULT CHARSET = UTF8;

# Triggers

DELIMITER $$

# Prevent the standing table from being modified by updating clubs

CREATE TRIGGER clubs_standing
BEFORE UPDATE ON Clubs
FOR EACH ROW
BEGIN
	IF (
		ISNULL(@autoUpdateClub)
		AND (
			NEW.goals_for <> OLD.goals_for 
			OR NEW.goals_against <> OLD.goals_against
			OR NEW.win <> OLD.win
			OR NEW.draw <> OLD.draw
			OR NEW.lose <> OLD.lose
		)
	) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Cannot modify standing table manually';
	END IF;
END;

$$

# Stored procedure to update standing table by a match

CREATE PROCEDURE update_standing(_clubid SMALLINT, _gf SMALLINT, _ga SMALLINT, _add TINYINT)
BEGIN
	SET @autoUpdateClub = 1;
	
	SET @W = _gf > _ga;
	SET @D = _gf = _ga;
	SET @L = _gf < _ga;
	
	UPDATE Clubs SET
		goals_for = goals_for + _gf * _add,
		goals_against = goals_against + _ga * _add,
		win = win + @W * _add,
		draw = draw + @D * _add,
		lose = lose + @L * _add
	WHERE clubid = _clubid;
	
	SET @autoUpdateClub = NULL;
END;

$$

# Update the standing table after inserting a match

CREATE TRIGGER matches_insert
AFTER INSERT ON Matches
FOR EACH ROW
BEGIN
	CALL update_standing(NEW.home_team, NEW.goals_home, NEW.goals_away, 1);
	CALL update_standing(NEW.away_team, NEW.goals_away, NEW.goals_home, 1);
END;

$$

# Update the standing table after deleting a match

CREATE TRIGGER matches_delete
AFTER DELETE ON Matches
FOR EACH ROW
BEGIN
	CALL update_standing(OLD.home_team, OLD.goals_home, OLD.goals_away, -1);
	CALL update_standing(OLD.away_team, OLD.goals_away, OLD.goals_home, -1);
END;

$$

# Update the standing table after modifying a match

CREATE TRIGGER matches_update
AFTER UPDATE ON Matches
FOR EACH ROW
BEGIN
	IF (
		OLD.home_team <> NEW.home_team
		OR OLD.away_team <> NEW.away_team
		OR OLD.goals_home <> NEW.goals_home
		OR OLD.home_team <> NEW.home_team
	) THEN
		CALL update_standing(OLD.home_team, OLD.goals_home, OLD.goals_away, -1);
		CALL update_standing(OLD.away_team, OLD.goals_away, OLD.goals_home, -1);
		
		CALL update_standing(NEW.home_team, NEW.goals_home, NEW.goals_away, 1);
		CALL update_standing(NEW.away_team, NEW.goals_away, NEW.goals_home, 1);
	END IF;
END;

$$

DELIMITER ;

# CHECK constraints

# Becuase MySQL does not support 'CREATE ASSERTION' statements,
# we always use 'ALTER TABLE .. ADD CONSTRAINT'.
# A football association manages at least one league.

ALTER TABLE Football_associations
ADD CONSTRAINT chk_fa_league
CHECK (
	NOT EXISTS (
		SELECT F.fid
		FROM Football_associations F
		WHERE NOT EXISTS (
			SELECT L.fid
			FROM Leagues L
			WHERE F.fid = L.fid
		)
	)
);

# A league has at least one club.

ALTER TABLE Leagues
ADD CONSTRAINT chk_leagues_club
CHECK (
	NOT EXISTS (
		SELECT L.lid
		FROM Leagues L
		WHERE NOT EXISTS (
			SELECT C.lid
			FROM Clubs C
			WHERE L.lid = C.lid
		)
	)
);
	
# A club has at least one player.

ALTER TABLE Clubs
ADD CONSTRAINT chk_clubs_player
CHECK (
	NOT EXISTS (
		SELECT C.clubid
		FROM Clubs C
		WHERE NOT EXISTS (
			SELECT P.clubid
			FROM Players P
			WHERE C.clubid = P.clubid
		)
	)
);

# A club employs at least one staff member.

ALTER TABLE Clubs
ADD CONSTRAINT chk_clubs_staff
CHECK (
	NOT EXISTS (
		SELECT C.clubid
		FROM Clubs C
		WHERE NOT EXISTS (
			SELECT S.clubid
			FROM Staff S
			WHERE C.clubid = S.clubid
		)
	)
);

# Normally there cannot be more than 4 teams to be upgraded
# or downgraded in the end of a soccer season.

ALTER TABLE Leagues
ADD CONSTRAINT chk_leagues_updown
CHECK (
	no_of_upgrade >= 0
	AND no_of_upgrade <= 4
	AND no_of_downgrade >= 0
	AND no_of_downgrade <= 4
);

# A soccer stadium usually has a capacity between 1000 and 200000.

ALTER TABLE Stadiums
ADD CONSTRAINT chk_stadiums_capacity
CHECK (
	capacity >= 1000
	AND capacity <= 200000
);

# The oldest soccer club which is still in professional leagues is Notts County, founded in 1862
# A soccer club listed in our database must be founded before the 2014/15 season starts.

ALTER TABLE Clubs
ADD CONSTRAINT chk_clubs_year
CHECK (
	year_founded >= 1862
	AND year_founded < 2015
);

# A European club can attend UEFA Champions League, UEFA Europa League or neither of them.

ALTER TABLE Clubs
ADD CONSTRAINT chk_clubs_european
CHECK (
	ISNULL(european_qualification)
	OR european_qualification = 'UEFA Champions League'
	OR european_qualification = 'UEFA Europa League'
);

# A soccer player cannot use 0 or an integer larger than 99 as his number.

ALTER TABLE Players
ADD CONSTRAINT chk_players_no
CHECK (
	no > 0
	AND no < 100
);

# A player can have one of these four positions:
# GK = Goalkeeper, DF = Defender, MF = Midfielder, FW = Forward.

ALTER TABLE Players
ADD CONSTRAINT chk_players_position
CHECK (
	position = 'GK'
	OR position = 'DF'
	OR position = 'MF'
	OR position = 'FW'
);

# A staff member has to be an owner, a chairperson, a (caretaker) manager or an assistant manager.
# In European continent, a soccer manager is usually called 'head coach',
# as they are more focused on tactics rather than finance.
# However, in our database, we still treat these head coaches as managers.

ALTER TABLE Staff
ADD CONSTRAINT chk_staff_type
CHECK (
	type = 'Owner'
	OR type = 'Chairperson'
	OR type = 'Co-chairperson'
	OR type = 'Manager'
	OR type = 'Assistant Manager'
	OR type = 'Caretaker Manager'
);

# The number of goals for one team must be between 0 and 149.
# The world record for the highest scoreline in professional association football is 149 : 0.

ALTER TABLE Matches
ADD CONSTRAINT chk_matches_goals
CHECK (
	goals_home >= 0
	AND goals_home <= 149
	AND goals_away >= 0
	AND goals_away <= 149
);

# A rating must be between 1 and 5.

ALTER TABLE Comments
ADD CONSTRAINT chk_comment_rating
CHECK (
	ISNULL(rating)
	OR (
		rating >= 1
		AND rating <= 5
	)
);

# A club's captain must be a player of this club.

ALTER TABLE Clubs
ADD CONSTRAINT chk_clubs_captain
CHECK (
	NOT EXISTS (
		SELECT *
		FROM Clubs C, Players P
		WHERE C.captain = P.pid
		AND C.clubid <> P.clubid
	)
);

# A league's last champion is not necessarily a member of this league.
# e.g. Leicester City is the last champion of English championship but now a member of the Premier League.
# An old champion can even become a member of other football association's league.
# e.g, Rapid Wien, a German champion in 1941 and CA Oradea, a Hungarian champion in 1943,
# are now affiliated with an Austrian and a Romanian league respectively

# A club's 'goals_for', 'goals_against', 'win', 'draw' and 'lose' attributes must be consistent with the matches.

ALTER TABLE Clubs
ADD CONSTRAINT chk_clubs_standing
CHECK (
	NOT EXISTS (
		SELECT *
		FROM (
			SELECT T1.clubid,
				SUM(T1.goals_for) AS goals_for,
				SUM(T1.goals_against) AS goals_against,
				SUM(T1.goals_for > T1.goals_against) AS win,
				SUM(T1.goals_for = T1.goals_against) AS draw,
				SUM(T1.goals_for < T1.goals_against) AS lose
			FROM (
				(
					SELECT
						M.home_team AS clubid,
						M.goals_home AS goals_for,
						M.goals_away AS goals_against
					FROM Matches M
				)
				UNION ALL
				(
					SELECT
						M.away_team AS clubid,
						M.goals_away AS goals_for,
						M.goals_home AS goals_against
					FROM Matches M
				)
			) T1
			GROUP BY T1.clubid
		) T2
		WHERE (T2.clubid, T2.goals_for, T2.goals_against, T2.win, T2.draw, T2.lose) NOT IN (
			SELECT C.clubid, C.goals_for, C.goals_against, C.win, C.draw, C.lose
			FROM Clubs C
		)
	)
);
