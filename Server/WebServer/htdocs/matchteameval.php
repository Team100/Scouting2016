<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - Evaluate Match
  //
  //
  //  Calls teaminfofields.inc to include team fields.  This allows sharing of the team info fields
  //    between the match eval and team info forms.
  //
  //
  require "page.inc";
  require "fieldnames.inc";

  // get variables
  $edit=$_GET["edit"];
  $final=$_GET["final"];
  $type=$_GET["type"];
  $matchnum=$_GET["matchnum"];
  $teamnum=$_GET["teamnum"];

  // header and setup
  pheader("Evaluate Team in Match {$type}-{$matchnum} Team: $teamnum", "titleonly");
  $connection = dbsetup();


  // if no teamnum, then select first team num for match
  if (! ($teamnum))
  {
    if (!($result = @ mysqli_query ($connection,
    	"select teamnum from match_team where type = '{$type}' and matchnum = {$matchnum} order by teamnum")))
	    dbshowerror($connection);
  	if (! ($row = mysqli_fetch_array($result)))
  		showerror("Match info not found.  Please try again.","die");
  	$teamnum = $row["teamnum"];
  }


	$matchidentifiers = fields_load("GET", array("event_id", "type", "matchnum", "teamnum"));

    // lock database, using two arrays for each table
    $dblock[0] = array(table=>"match_team",where=>" event_id = '{$matchidentifiers["event_id"]}' and type = '{$matchidentifiers["type"]}' and matchnum = '{$matchidentifiers["matchnum"]}' and teamnum = '{$matchidentifiers["teamnum"]}' ");
  	$dblock[1] = array(table=>"teambot",where=>"event_id = '{$def_event_id}' and teamnum = {$teamnum}");

	$match_sql_identifier = "event_id = '{$matchidentifiers["event_id"]}' and type = '{$matchidentifiers["type"]}'
		and matchnum = {$matchidentifiers["matchnum"]}";
	$team_sql_identifier = "teamnum={$teamnum}";


	// define fields used
	// if fields positions matter, start with position
	if ($field_positions === TRUE)
		$field_array = array("position");
	else
		$field_array = array();

	$field_array = array_merge ($field_array, array("rating_offense", "rating_defense", "raw_points",
		"penalties","match_notes", "match_offense_analysis", "match_defense_analysis","match_pos_analysis",
		"match_with_recommendation","match_against_recommendation"),
  		param_array("Match"));

	// teambot array
	  $table_teambot = array_merge ( array("rank_overall","rating_overall","rating_overall_off","rating_overall_def",
	  	"rank_pos1","rating_pos1","rank_pos2","rating_pos2","rank_pos3","rating_pos3","offense_analysis",
	  	"defense_analysis","pos1_analysis","pos2_analysis","pos3_analysis","robot_analysis","driver_analysis",
	  	"with_recommendation","against_recommendation"),
  		param_array("Play"), param_array("Pit"));


	// handle update if returning from edit mode
	if ($edit == 2)
	{
  		if ( $_POST[op] == "Save" )
  		{

  			// check row
  			dblock($dblock,"changedby");
  			dblock($dblock,"check");

	  		// load operation
	  		// match_team table
			$formfields = fields_load("post",$field_array);
			$query = "update match_team set " . fields_insert("update",$formfields) . " where {$match_sql_identifier} and {$team_sql_identifier}";

			// process query
			if (! (@mysqli_query ($connection, $query) ))
				dbshowerror($connection, "die");

			// teambot info
			// load form fields
			$formfields = fields_load("post", $table_teambot);

			$query = "update teambot set " . fields_insert("update",$formfields) . " where event_id = '{$def_event_id}' and teamnum = {$teamnum}";
			// process query
			if (! (@mysqli_query ($connection, $query) ))
				dbshowerror($connection, "die");

			// commit
			if (! (@mysqli_commit($connection) ))
				dbshowerror($connection, "die");

		}

		// abondon lock
		dblock($dblock,"abandon");

		// update completed
		$edit = 0;
	}

	// lock tables if in edit mode
	if ($edit) dblock($dblock,"lock");  // lock row with current user id
	// define edit URL
	$editURL = "/matchteameval.php?teamnum={$teamnum}&event_id={$event_id}&type={$type}&matchnum={$matchnum}";



  //
  // print top of page
  //

  // get basic teaminfo details define result set
  if (!($result = @ mysqli_query ($connection,
  	"select name, nickname from team where teamnum = {$teamnum}")))
    dbshowerror($connection);
  // get row
  $row = mysqli_fetch_array($result);
  $teamname = $row["name"];
  $teamnickname = $row["nickname"];

  // print team number, name and nickname as header
  print "<H2>Match Team Evaluation {$type}-{$matchnum} &nbsp;&nbsp; ";
  print teamhref($teamnum) . "{$teamnum} - {$teamname}</a>";
  if ($teamnickname) print "({$teamnickname})";
  print "</H2>\n";


  // frame top commands and match info in layout table
  print "<table valalign=\"top\">\n<tr valign=\"top\">\n<td>\n";

  // next and prev buttons
   // see if previous match exists and display buttong
    $matchnum_text = $matchnum - 1;
    $query = "select matchnum from match_instance where type = '{$type}' and matchnum = {$matchnum_text}";
    if (!($result = @ mysqli_query ($connection, $query)))
      dbshowerror($connection);

    // get row
  	if ($row = mysqli_fetch_array($result))
  		print "<a href=\"/matchteameval.php?event_id={$event_id}&type={$type}&matchnum={$matchnum_text}\">&lt Prev</a> &nbsp;&nbsp;&nbsp;";

   // see if next match exists and display
    $matchnum_text = $matchnum + 1;
    $query = "select matchnum from match_instance where type = '{$type}' and matchnum = {$matchnum_text}";
    if (!($result = @ mysqli_query ($connection, $query)))
      dbshowerror($connection);

    // get row
  	if ($row = mysqli_fetch_array($result))
  		print "<a href=\"/matchteameval.php?event_id={$event_id}&type={$type}&matchnum={$matchnum_text}\">Next &gt</a> &nbsp;&nbsp;";
	print "<br>";


  // view match details
  print "<a href=\"/matcheval.php?final={$final}&event_id={$matchidentifiers["event_id"]}&
		type={$matchidentifiers["type"]}&matchnum={$matchidentifiers["matchnum"]}\">View Match Details</a><br>";

  // view
  print "<a href=\"/matchlist.php?final={$final}\">Match List</a><br>";
  print "<a href=\"/matchlist.php?final={$final}&highlight={$teamnum}\">View in match list</a><br><br>";

	if ($edit)
	{
		// if in edit mode, signal save with edit=2
		print "<form method=\"POST\" action=\"/matchteameval.php?edit=2&teamnum={$teamnum}&event_id={$matchidentifiers["event_id"]}
			&type={$matchidentifiers["type"]}&matchnum={$matchidentifiers["matchnum"]}\">\n";
	}

	// show edit block
    print dblockshowedit($edit, $dblock,$editURL);



  //
  // close first cell and space between next layout
  print "\n</td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>\n";

	//print teams
	print "<table border=\"1\"><tr>Teams in Match:</tr><tr>";
	//prep for displaying colors for the teams
	//query to get color codes for teams
	$detail_query = "select type, matchnum, teamnum, color from match_team"
		. " where matchnum = {$_GET["matchnum"]} "
		. " order by color DESC, matchnum";

	if (!($detail = @ mysqli_query ($connection, $detail_query )))
		dbshowerror($connection);

	//create array of upcoming teams
	$query = "select  a.type, a.matchnum, b.teamnum from match_team a, match_team b
		where a.type=b.type and a.matchnum=b.matchnum and a.color=b.color and
		a.teamnum=3006 group by teamnum order by teamnum,  matchnum";

	if (!($result = @ mysqli_query ($connection, $query)))
		dbshowerror($connection);
	while ($row = mysqli_fetch_array($result))
	{
		$upcoming[$row['teamnum']]['type'] = $row['type'];
		$upcoming[$row['teamnum']]['with_matchnum'] = $row['matchnum'];
	}

	// load teams we are playing against
	$query = "select  a.type, a.matchnum, b.teamnum from match_team a, match_team b
		where a.type=b.type and a.matchnum=b.matchnum and a.color!=b.color and
		a.teamnum=3006 group by teamnum order by teamnum,  matchnum";

	if (!($result = @ mysqli_query ($connection, $query)))
		dbshowerror($connection);
	while ($row = mysqli_fetch_array($result))
	{
		$upcoming[$row['teamnum']]['type'] = $row['type'];
		$upcoming[$row['teamnum']]['against_matchnum'] = $row['matchnum'];
	}
	//end of creating upcoming teams array

	print "<td>Red</td>";
	$counter=0;
	while ($detailrow = mysqli_fetch_array($detail))
	{
		// set teamnumT
		$teamnumT = $detailrow['teamnum'];

		// start output of individual cell
		print "<td";

		// the the host team, mark with color
		if ( $teamnumT == $host_teamnum)
			print " style=\"background-color: {$lyellow}\" ";

		// otherwise check whether we're playing with or against them, and the right type
		else if (array_key_exists($teamnum, $upcoming) && ($detailrow['type'] == $upcoming[$teamnum]['type']))
			// if playing agaist and with, then blue
			if (($detailrow['matchnum'] < $upcoming[$teamnumT]['with_matchnum']) &&
				($detailrow['matchnum'] < $upcoming[$teamnumT]['against_matchnum']))
				print " style=\"background-color: {$lblue}\" ";
			// else if with
			else if ($detailrow['matchnum'] < $upcoming[$teamnumT]['with_matchnum'])
				print " style=\"background-color: {$lgreen}\" ";
			else if ($detailrow['matchnum'] < $upcoming[$teamnumT]['against_matchnum'])
				print " style=\"background-color: {$lred}\" ";

		if($teamnum == $teamnumT)
			print "> <b>{$row["color"]}{$teamnumT}{$editor}</td>";
		else
			print ">{$row["color"]} <a href=\"/matchteameval.php?teamnum={$teamnumT}&event_id={$matchidentifiers["event_id"]}&
					type={$matchidentifiers["type"]}&matchnum={$matchidentifiers["matchnum"]}\">{$teamnumT}{$editor}</a></td>";

		$counter++;
		if($counter==3)
			print "</tr><tr><td>Blue</td>";
	}


	print "</tr></table>\n";

	// next format block
    print "\n</td><td>&nbsp;&nbsp;</td><td>\n";

	print "
	Match Info:
	<!---General Match Info Display--->
	<table valign=\"top\" border=1>
	";  // end of print

		$query = "select event_id, type, matchnum, scheduled_time, actual_time
			from match_instance where ".$match_sql_identifier;

		if (! ($result = @ mysqli_query ($connection, $query) ))
			dbshowerror($connection, "die");
		if (! ($resultR = @ mysqli_query ($connection, "select score from match_instance_alliance where {$match_sql_identifier} and color='R'") ))
			dbshowerror($connection, "die");
		if (! ($resultB = @ mysqli_query ($connection, "select score from match_instance_alliance where {$match_sql_identifier} and color='B'") ))
			dbshowerror($connection, "die");

		$row = mysqli_fetch_array($result);
		$pointsR = mysqli_fetch_array($resultR);
		$pointsB = mysqli_fetch_array($resultB);

		//print match data
		print "<tr><td>League</td><td>Type</td><td>Match</td><td>Sched Time</td><td>Actual Time</td><td>Red Points</td><td>Blue Points</td></tr>";
		print "<tr><td>".$row["event_id"]."</td><td>".$row["type"]."</td><td>".$row["matchnum"]."</td><td>"
			.substr($row["scheduled_time"],0,5)."</td><td>".substr($row["actual_time"],0,5)
			."</td><td>".$pointsR["score"]."</td><td>".$pointsB["score"]."</td></tr>";

print <<< EOF_EOF

<!--- end match info --->
</tr></table>

<!--- end top header --->
</td></tr></table>

EOF_EOF
; // end of print




 // **********************************************************************************
  //
  // include team info form
  //

  // get row info
    // get team details define result set
    if (!($result = @ mysqli_query ($connection,
    	"select ". fields_insert("nameonly",NULL,$table_teambot) . " from teambot where event_id = '{$def_event_id}' and teamnum = {$teamnum}")))
      dbshowerror($connection);

    // get row
  	$row = mysqli_fetch_array($result);

  // print team number, name and nickname as header
  print "<hr><H3>Team Robot Info - {$teamnum} - {$teamname}";
  if ($teamnickname) print "({$teamnickname})";
  print "</H3>\n";

  require "teaminfofields.inc";
  //
  // ***********************************************************************************


  //
  // team specific section
  //

print <<< EOF_EOF
<!--- match-specific section --->
<hr>
<b>Team Analysis Specific to this Match - {$type}-{$matchnum} &nbsp;&nbsp; {$teamnum} - {$teamname}
EOF_EOF
; // end of print

if ($teamnickname) print "({$teamnickname})";

print <<< EOF_EOF
  </b>
<br>

<!---- Table layout ---->
<table valign="top">
<tr valign="top"><td>

Team Evaluation:
<!---Individual Team Evaluation--->
EOF_EOF
; // end of print


  	$query = "select ". fields_insert("nameonly",NULL,$field_array) . " from match_team where {$match_sql_identifier} and {$team_sql_identifier}";

	if (! ($result = @ mysqli_query ($connection, $query)))
		dbshowerror($connection, "die");
	$row = mysqli_fetch_array($result);

	print "<table border=\"1\">";

	$options["tr"] = 1;  // add tr tags
	print fill_tab_text_field($edit, $options, $row, $match_team_name, $match_team_size);

	print "</table>
	<!--- table layout to other column --->
	</td><td>&nbsp;&nbsp;</td><td>
	"; // end of print

	//
	// Match-specific fields
	//

	  // Per match variables
	  print "
	  <b>Match-specific Variables:</b>
	  <!--- layout table --->
	  <table><tr valign=\"top\"><td>

	  <table border=\"1\" valign=\"top\">
	   ";  // end of print

	  // get play variables
	  $options["tr"]=TRUE;
	  $options["notag"]=FALSE;
	  $options["pagebreak"]=3;
	  $options["pagebreakstring"]="\n</table>\n</td><td>\n<table border=\"1\" valign=\"top\">";
	  print tabparamfields($edit, $options, $row, "Match");

  // end blocks of data, table layout
  print "\n</table>
    </td></tr></table>";

    // show photo
	if ( file_exists ("teamimages/team-{$teamnum}-med.jpg"))
	    print "</td>\n<td>
	    <img src=\"/teamimages/team-{$teamnum}-med.jpg\" alt=\"Team ${teamnum} thumb\" title=\"Team {$teamnum}\" width=\"80\" height=\"80\"/>
        "; // end of print

   // end outer table layout
   print "\n</td></tr></table>\n";


	//
	// full text field input
	//
// analysis table format
  $options["notag"]=FALSE;
  print "<table>
  <tr>
  <td>";

  // print out variables through table print
  print tabtextarea($edit,$options,$row, "match_notes","Notes and additions specific to this match:",8,100);

  // close table
  print "</td></tr></table>\n";


  // show edit block again
      print dblockshowedit($edit, $dblock, $editURL);




  // close the form if in edit mode
  if ($edit) print "\n</form>\n";

	showupdatedby($dblock);

	pfooter();
?>