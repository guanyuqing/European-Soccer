var currentPlayerId = 0;
var afterCommentId = 0;

var defaultFaId = faId;
var defaultLeagueId = leagueId;

var defaultNavFaName;

var defaultNavLeagueName;
var defaultNavLeagueMenu;

var defaultNavClubMenu;

var starCaptionClasses = {
	0.1: 'label label-danger',
	0.2: 'label label-danger',
	0.3: 'label label-danger',
	0.4: 'label label-danger',
	0.5: 'label label-danger',
	0.6: 'label label-danger',
	0.7: 'label label-danger',
	0.8: 'label label-danger',
	0.9: 'label label-danger',
	1: 'label label-danger',
	1.1: 'label label-warning',
	1.2: 'label label-warning',
	1.3: 'label label-warning',
	1.4: 'label label-warning',
	1.5: 'label label-warning',
	1.6: 'label label-warning',
	1.7: 'label label-warning',
	1.8: 'label label-warning',
	1.9: 'label label-warning',
	2: 'label label-warning',
	2.1: 'label label-info',
	2.2: 'label label-info',
	2.3: 'label label-info',
	2.4: 'label label-info',
	2.5: 'label label-info',
	2.6: 'label label-info',
	2.7: 'label label-info',
	2.8: 'label label-info',
	2.9: 'label label-info',
	3: 'label label-info',
	3.1: 'label label-primary',
	3.2: 'label label-primary',
	3.3: 'label label-primary',
	3.4: 'label label-primary',
	3.5: 'label label-primary',
	3.6: 'label label-primary',
	3.7: 'label label-primary',
	3.8: 'label label-primary',
	3.9: 'label label-primary',
	4: 'label label-primary',
	4.1: 'label label-success',
	4.2: 'label label-success',
	4.3: 'label label-success',
	4.4: 'label label-success',
	4.5: 'label label-success',
	4.6: 'label label-success',
	4.7: 'label label-success',
	4.8: 'label label-success',
	4.9: 'label label-success',
	5: 'label label-success'
};

$.tablesorter.themes.bootstrap = {
	iconSortNone:'bootstrap-icon-unsorted',
	iconSortAsc:'icon-chevron-up glyphicon glyphicon-chevron-up',
	iconSortDesc:'icon-chevron-down glyphicon glyphicon-chevron-down'
};

function loadPlayers()
{
	$.ajax(
	{
		url: 'op/loadPlayersByClub.php',
		type: 'POST',
		data: {
			id: clubId
		},
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, player) {
				var tdName = $("<td>").text(player.name);
				
				var tr = $("<tr data-toggle=\"modal\" href=\"#comment-modal\" data-playerid=\"" +
					player.playerId + "\" data-playername=\"" + tdName.html() + "\">");
				
				var playerId = player.playerId;
				
				var tdNo = $("<td>").text(player.no);
				
				var tdNationality = $("<td>").html("<img src=\"img/nation/" + player.nationality +
					".png\" class=\"national-flag\">" + player.nationality);
				
				var tdPosition = $("<td>").text(player.position);
				
				var tdRating = $("<td>");
				var inputRating = $("<input id=\"rating-" + playerId + "\" type=\"number\" class=\"rating\"/>");
				tdRating.append(inputRating);
				
				if (playerId == captainId)
				{
					tdName.append("&nbsp;<span class=\"label label-primary\">C</span>");
				}
				
				tr.append(tdNo)
					.append(tdName)
					.append(tdNationality)
					.append(tdPosition)
					.append(tdRating);
					
				$("#squad").append(tr);
				
				setPlayerRating(player.playerId, player.rating);
			});
			
			$("#table-squad").tablesorter({
				headerTemplate:'{content} {icon}',
				sortList:[[0,0]],
				theme:"bootstrap",
				widgets:[ "uitheme" ],
				widthFixed:true
			});
			
			
		}	
	});	
}

function loadClubInfo()
{
	$.ajax(
	{
		url: 'op/loadClubInfoById.php',
		type: 'POST',
		data: {
			id: clubId
		},
		dataType: 'json',
		success: function(data)
		{
			var basicInfo = $("#basic-info");
			var stadiumInfo = $("#stadium-info");
			
			var tr = $("<tr>");
			tr.append($("<td class=\"col-md-6 field-name\">").text("Year Founded"))
				.append($("<td>").text(data.yearFounded));
			
			basicInfo.append(tr);
			
			if (data.europeanQualification)
			{
				tr = $("<tr>");
				tr.append($("<td class=\"col-md-6 field-name\">").text("European Competition"))
					.append($("<td>").text(data.europeanQualification));
				
				basicInfo.append(tr);
			}
			
			if (data.captain)
			{
				var tdName = $("<td>").text(data.captain.name);
				
				tr = $("<tr>");
				tr.append($("<td class=\"col-md-6 field-name\">").text("Captain"))
					.append($("<td id=\"captain\" data-toggle=\"modal\" href=\"#comment-modal\" data-playerid=\"" +
						data.captain.playerId + "\" data-playername=\"" + tdName.html() + "\">").html("<img src=\"img/nation/" +
						data.captain.nationality + ".png\" class=\"national-flag\">" + tdName.html()));
				
				basicInfo.append(tr);
			}
			
			if (!data.stadium)
			{
				tr = $("<tr>");
				tr.append($("<td>").text("No stadium"));
				
				stadiumInfo.append(tr);
			}
			else
			{
				tr = $("<tr>");
				tr.append($("<td class=\"col-md-6 field-name\">").text("Name"))
					.append($("<td>").text(data.stadium.name));
				
				stadiumInfo.append(tr);
				
				tr = $("<tr>");
				tr.append($("<td class=\"col-md-6 field-name\">").text("City"))
					.append($("<td>").text(data.stadium.city + ", " + data.stadium.country));
				
				stadiumInfo.append(tr);
				
				tr = $("<tr>");
				tr.append($("<td class=\"col-md-6 field-name\">").text("Capacity"))
					.append($("<td>").text(data.stadium.capacity));
				
				stadiumInfo.append(tr);
			}
			
			$.each(data.staffs, function(i, staff) {
				var staffInfo = $("#staff-info");
				
				var tdName = $("<td>").text(staff.name);
				
				var tr = $("<tr>");
				tr.append($("<td class=\"col-md-6 field-name\">").text(staff.type))
					.append($("<td>").html("<img src=\"img/nation/" +
						staff.nationality + ".png\" class=\"national-flag\">" + tdName.html()));
				
				staffInfo.append(tr);
			});
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
	location.href = "leagueview.php?id=" + id;	
}

function switchNavClub(id)
{
	if (clubId == id)
	{
		return;
	}
			
	location.href = "clubview.php?id=" + id;	
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
			
			if (!defaultNavLeagueName)
			{
				defaultNavLeagueName = $("#nav-league-name").html();
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

function loadNavClubs()
{
	$("#nav-club-menu").empty();
	
	$.ajax(
	{
		url: 'op/loadNames.php',
		type: 'POST',
		data: {
			type: "club", id: leagueId
		},
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, club) {
				if (club.clubId == clubId)
				{
					$("#nav-club-name").text(club.name).append(" <span class=\"caret\"></span>");
				}
				
				var td = $("<td>").text(club.name);
				
				var li = $("<li>");
				var a = $("<a href=\"javascript:switchNavClub(" + club.clubId + ")\">").html("<img class=\"menu-logo\" src=\"img/club/" +
					club.clubId + ".png\" /> " + td.html());
				
				li.append(a);
				$("#nav-club-menu").append(li);
			});
			
			if (!defaultNavClubMenu)
			{
				defaultNavClubMenu = $("#nav-club-menu").html();
			}
		}
	});
}

function loadAndShowNavClubs()
{
	$("#nav-club-menu").empty();
	
	$.ajax(
	{
		url: 'op/loadNames.php',
		type: 'POST',
		data: {
			type: "club", id: leagueId
		},
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, club) {
				if (club.clubId == clubId)
				{
					$("#nav-club-name").text(club.name).append(" <span class=\"caret\"></span>");
				}
				
				var td = $("<td>").text(club.name);
				
				var li = $("<li>");
				var a = $("<a href=\"javascript:switchNavClub(" + club.clubId + ")\">").html("<img class=\"menu-logo\" src=\"img/club/" +
					club.clubId + ".png\" /> " + td.html());
				
				li.append(a);
				$("#nav-club-menu").append(li);
			});
			
			$("#nav-club-name").dropdown("toggle");
		}
	});
}

function switchCommentModal(e)
{
	currentPlayerId = e.relatedTarget.dataset.playerid;
	$("#new-comment-player").val(currentPlayerId);
	
	var playerName = e.relatedTarget.dataset.playername;
	$("#modal-player-name").html(playerName);
	
	resetNewComment();
	
	afterCommentId = 0;
	$("#previous-comment").empty();
	
	$("#player-photo").attr("src", "op/loadPlayerPhoto.php?id=" + currentPlayerId);
	
	loadComments();
}

function loadComments()
{
	$.ajax(
	{
		url: 'op/loadCommentsByPlayer.php',
		type: 'POST',
		data: {
			id: currentPlayerId, after: afterCommentId
		},
		dataType: 'json',
		success: function(data)
		{
			if (data.length == 0)
			{
				var previousComment = $("#previous-comment");
				if (previousComment.children().length == 0)
				{
					previousComment.append($("<tr>").append($("<td>").text("No previous comments")));
				}
			}
			else
			{
				if (afterCommentId == 0)
				{
					$("#previous-comment").empty();
				}
				
				afterCommentId = data[data.length - 1].commentId;
				
				$.each(data, function(i, comment) {
					var tr = $("<tr>");
					
					var tdCommenter = $("<td>");
					if (comment.commenterName)
					{
						tdCommenter.text(comment.commenterName);
					}
					else
					{
						tdCommenter.html("<em>Anonymous</em>");
					}
					
					var tdRating = $("<td>");
					if (comment.rating)
					{
						var inputRating = $("<input type=\"number\" class=\"rating\"/>");
						tdRating.append(inputRating);
						
						inputRating.rating({
							'readonly': true,
							'stars':'5',
							'min':'0',
							'max':'5',
							'step':'0.5',
							'size':'xs',
							'starCaptions':{ 0:'No rating' },
					   	'showClear': false
						});
						
						inputRating.rating("update", comment.rating);
					}
					else
					{
						tdRating.html("<em>N/A</em>");
					}
					
					var tdContent = $("<td>").text(comment.content);
					
					tr.append(tdCommenter)
						.append(tdRating)
						.append(tdContent);
						
					$("#previous-comment").prepend(tr);
				});
			}	
		}	
	});
}

function initNewComment()
{
	$('#comment-rating').rating({
		'showCaption':true,
		'stars':'5',
		'min':'0',
		'max':'5',
		'step':'1',
		'size':'sm',
		'starCaptions':{ 0:'No rating' }
	});
	
	$('#new-comment').bootstrapValidator({
		feedbackIcons: {
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'
		},
		excluded: [':disabled'],
		fields: {
			content: {
				validators: {
					notEmpty: {
						message: 'The content of your comment cannot be empty.'
					}
				}
			}
		}
	});
}

function submitComment()
{
	$.ajax(
	{
		url: 'op/insertNewComment.php',
		type: 'POST',
		data: $('#new-comment').serialize(),
		dataType: 'json',
		success: function(data) {
			resetNewComment();
			loadComments();
			
			setPlayerRating(data.playerId, data.rating);
			$("#table-squad").trigger("update", true);
		}
	});
}

function setPlayerRating(playerId, rating)
{
	var inputRating = $("#rating-" + playerId);
	
	inputRating.rating({
		'readonly': true,
		'stars':'5',
		'min':'0',
		'max':'5',
		'step':'0.1',
		'size':'xs',
		'starCaptions':{ 0:'No rating' },
   	'showClear': false,
		'starCaptionClasses': starCaptionClasses
	});
	
	rating = Math.round(rating * 10) / 10.0;	
	inputRating.rating("update", rating);
}

function resetNewComment()
{
	$('#new-comment')[0].reset();
	$('#comment-rating').val(0);
	$('#new-comment')
		.bootstrapValidator('updateStatus', 'content', 'NOT_VALIDATED')
		.bootstrapValidator('validateField', 'content');
}

loadNavFas();
loadNavLeagues();
loadNavClubs();

loadPlayers();
loadClubInfo();

initNewComment();

$('#comment-modal').on('show.bs.modal', switchCommentModal);

$('#main').click(function() {
	var flag = false;
	
	if (faId != defaultFaId)
	{
		faId = defaultFaId;
		flag = true;
	}
	
	if (leagueId != defaultLeagueId)
	{
		leagueId = defaultLeagueId;
		flag = true;
	}
	
	if (flag)
	{
		if (defaultNavFaName)
		{
			$("#nav-fa-name").html(defaultNavFaName);
		}
		
		if (defaultNavLeagueName)
		{
			$("#nav-league-name").html(defaultNavLeagueName);
			$("#nav-league-menu").html(defaultNavLeagueMenu);
		}
		
		if (defaultNavClubMenu)
		{
			$("#nav-club-menu").html(defaultNavClubMenu);
		}
	}
});

