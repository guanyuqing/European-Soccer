var defaultFaId = faId;

var defaultNavFaName;
var defaultNavLeagueMenu;

var currentDate;

$.tablesorter.themes.bootstrap = {
	iconSortNone:'bootstrap-icon-unsorted',
	iconSortAsc:'icon-chevron-up glyphicon glyphicon-chevron-up',
	iconSortDesc:'icon-chevron-down glyphicon glyphicon-chevron-down'
};

function gotoClub(event)
{
	location.href = "clubview.php?id=" + event.data.id;
}

function loadClubs()
{
	$.ajax(
	{
		url: 'op/loadClubByLeague.php',
		type: 'POST',
		data: {
			id: leagueId
		},
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, club)
			{
				var tr = $("<tr>");
				
				var tdNo = $("<td>").text(club.rank);
				
				var tdName = $("<td>").text(club.name);
				tdName.html("<img class=\"table-logo\" src=\"img/club/" + club.clubId + ".png\" /> " + tdName.html())
				
				var tdPlayed = $("<td class=\"standing-num\">").text(club.played);
				
				var tdGF = $("<td class=\"standing-num\">").text(club.gf);
				var tdGA = $("<td class=\"standing-num\">").text(club.ga);
				var tdGD = $("<td class=\"standing-num\">").text(club.gd);
				
				var tdWon = $("<td class=\"standing-num\">").text(club.won);
				var tdDrawn = $("<td class=\"standing-num\">").text(club.drawn);
				var tdLost = $("<td class=\"standing-num\">").text(club.lost);
				
				var tdPts = $("<td class=\"standing-num\">").text(club.pts);
				
				tr.append(tdNo)
					.append(tdName)
					.append(tdPlayed)
					.append(tdWon)
					.append(tdDrawn)
					.append(tdLost)
					.append(tdGF)
					.append(tdGA)
					.append(tdGD)
					.append(tdPts);
					
				tr.click({id: club.clubId }, gotoClub);
				
				$("#standing").append(tr);
				
			});
			
			$("#table-standing").tablesorter({
				headerTemplate:'{content} {icon}',
				sortList:[[0,0]],
				theme:"bootstrap",
				widgets:[ "uitheme" ],
				widthFixed:true
			});
			
			var len = data.length;
			var children = $("#standing").children();
			
			for (var i = 0; i < up; ++i)
			{
				children.eq(i).addClass("up");
			}
			
			for (var i = len - down; i < len; ++i)
			{
				children.eq(i).addClass("down");
			}
		}	
	});	
}

function loadDatesAndMatches()
{
	$.ajax(
	{
		url: 'op/loadDatesAndMatchesByLeague.php',
		type: 'POST',
		data: {
			id: leagueId,
		},
		dataType: 'json',
		success: function(data)
		{
			$.each(data.dates, function(i, date)
			{
				if (!currentDate)
				{
					currentDate = date;
				}
				
				var option = $("<option>").text(date);
				$("#match-date").append(option);
			});
			
			if (currentDate)
			{
				setMatches(data.matches);
			}
		}	
	});	
}

function setMatches(matches)
{
	$.each(matches, function(i, match)
	{
		var tr = $("<tr>");
		
		var tdHomeRank = $("<td class=\"col-sm-1\">").text(match.homeRank);
		
		var tdHomeName = $("<td class=\"col-sm-5 match-team home-team\">").text(match.homeName);
		tdHomeName.html(tdHomeName.html() + " <img class=\"table-logo\" src=\"img/club/" + match.homeId + ".png\" />")
		tdHomeName.click({id: match.homeId }, gotoClub);
		
		var tdGoal = $("<td class=\"col-sm-1 match-goal\">").text(match.homeGoal + " : " + match.awayGoal);
		
		var tdAwayName = $("<td class=\"col-sm-5 match-team\">").text(match.awayName);
		tdAwayName.html("<img class=\"table-logo\" src=\"img/club/" + match.awayId + ".png\" /> " + tdAwayName.html())
		tdAwayName.click({id: match.awayId }, gotoClub);
		
		var tdAwayRank = $("<td class=\"col-sm-1 away-rank\">").text(match.awayRank);		
		
		tr.append(tdHomeRank)
			.append(tdHomeName)
			.append(tdGoal)
			.append(tdAwayName)
			.append(tdAwayRank)
		
		$("#matches").append(tr);
	});
}

function switchDate()
{
	currentDate = $("#match-date").val();
	loadMatchesByDate();
}

function loadMatchesByDate()
{
	$("#matches").empty();
		
	$.ajax(
	{
		url: 'op/loadMatchesByLeagueAndDate.php',
		type: 'POST',
		data: {
			id: leagueId,
			date: currentDate
		},
		dataType: 'json',
		success: function(data)
		{
			setMatches(data);
		}	
	});	
}

function switchNavFa(id)
{
	if (faId == id)
	{
		$("#nav-league-name").dropdown("toggle");
		return;
	}
	
	faId = id;
	
	loadNavFas();
	loadAndShowNavLeagues();
}

function switchNavLeague(id)
{
	if (leagueId == id)
	{
		return;
	}
	
	location.href = "leagueview.php?id=" + id;	
}

function switchNavDate(date)
{
	if (matchDate == date)
	{
		return;
	}
			
	location.href = "matchview.php?id=" + leagueId + "&dat=" + date;	
}

function loadNavFas()
{
	$("#nav-fa-menu").empty();
	
	$.ajax(
	{
		url: 'op/loadNames.php',
		type: 'POST',
		data: {
			type: "fa"
		},
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, fa) {
				if (fa.faId == faId)
				{
					$("#nav-fa-name").text(fa.name).append(" <span class=\"caret\"></span>");
				}
				
				var td = $("<td>").text(fa.name);
				
				var li = $("<li>");
				var a = $("<a href=\"javascript:switchNavFa(" + fa.faId + ")\">").html("<img class=\"menu-logo\" src=\"img/fa/" +
					fa.faId + ".png\" /> " + td.html());
				
				li.append(a);
				$("#nav-fa-menu").append(li);
			});
			
			if (!defaultNavFaName)
			{
				defaultNavFaName = $("#nav-fa-name").html();
			}
		}
	});
}

function loadNavLeagues()
{
	$("#nav-league-menu").empty();
	
	$.ajax(
	{
		url: 'op/loadNames.php',
		type: 'POST',
		data: {
			type: "league", id: faId
		},
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, league) {
				if (league.leagueId == leagueId)
				{
					$("#nav-league-name").text(league.name).append(" <span class=\"caret\"></span>");
				}
				
				var td = $("<td>").text(league.name);
				
				var li = $("<li>");
				var a = $("<a href=\"javascript:switchNavLeague(" + league.leagueId + ")\">").html("<img class=\"menu-logo\" src=\"img/league/" +
					league.leagueId + ".png\" /> " + td.html());
				
				li.append(a);
				$("#nav-league-menu").append(li);
			});
			
			if (!defaultNavLeagueMenu)
			{
				defaultNavLeagueMenu = $("#nav-league-menu").html();
			}
		}
	});
}

function loadAndShowNavLeagues()
{
	$("#nav-league-menu").empty();
	
	$.ajax(
	{
		url: 'op/loadNames.php',
		type: 'POST',
		data: {
			type: "league", id: faId
		},
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, league) {
				if (league.leagueId == leagueId)
				{
					$("#nav-league-name").text(league.name).append(" <span class=\"caret\"></span>");
				}
				
				var td = $("<td>").text(league.name);
				
				var li = $("<li>");
				var a = $("<a href=\"javascript:switchNavLeague(" + league.leagueId + ")\">").html("<img class=\"menu-logo\" src=\"img/league/" +
					league.leagueId + ".png\" /> " + td.html());
				
				li.append(a);
				$("#nav-league-menu").append(li);
			});
			
			$("#nav-league-name").dropdown("toggle");
		}
	});
}

loadNavFas();
loadNavLeagues();

loadClubs();
loadDatesAndMatches();


$('#main').click(function() {

	var flag = false;
	
	if (faId != defaultFaId)
	{
		faId = defaultFaId;
		flag = true;
	}
	
	if (flag)
	{
		if (defaultNavFaName)
		{
			$("#nav-fa-name").html(defaultNavFaName);
		}
		
		if (defaultNavLeagueMenu)
		{
			$("#nav-league-menu").html(defaultNavLeagueMenu);
		}	
	}
});

