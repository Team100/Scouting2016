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
  pheader("{$teamnum} Match {$type}-{$matchnum} - Evaluate Team in Match", "titleonly");
  $connection = dbsetup();


  // if no teamnum, then select first team num for match
  if (! ($teamnum))
  {
    $query = "select teamnum from match_team where event_id = '{$sys_event_id}' and type = '{$type}'
          and matchnum = {$matchnum} order by teamnum";

    if (debug()) print "<br>DEBUG-matchteameval, no teameval: " . $query . "<br>\n";
    if (!($result = @ mysqli_query ($connection, $query)))
	    dbshowerror($connection);
  	if (! ($row = mysqli_fetch_array($result)))
  		showerror("Match info not found.  Please try again.","die");
  	$teamnum = $row["teamnum"];
  }

  // set up for needs eval flag
  if (test_user_prop("needeval")) $teams_need_eval = allteams_need_eval();


	$matchidentifiers = fields_load("GET", array("event_id", "type", "matchnum", "teamnum"));

    // lock database, using two arrays for each table
    $dblock[0] = array(table=>"match_team",where=>" event_id = '{$sys_event_id}' and type = '{$matchidentifiers["type"]}' and matchnum = '{$matchidentifiers["matchnum"]}' and teamnum = '{$matchidentifiers["teamnum"]}' ");
  	$dblock[1] = array(table=>"teambot",where=>"event_id = '{$sys_event_id}' and teamnum = {$teamnum}");

	$match_sql_identifier = "event_id = '{$sys_event_id}' and type = '{$matchidentifiers["type"]}'
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
	  	param_array("Play"));


    // handle time set mode
    if ((isset($_POST['op'])) && ($_POST['op'] == "Set Time"))
      match_set_time($matchidentifiers["type"], $matchidentifiers["matchnum"]);


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
			if (debug()) print "<br>DEBUG-matchteameval: " . $query . "<br>\n";

			// process query
			if (! (@mysqli_query ($connection, $query) ))
				dbshowerror($connection, "die");

			// teambot info
			// load form fields
			$formfields = fields_load("post", $table_teambot);

			$query = "update teambot set " . fields_insert("update",$formfields) . " where event_id = '{$sys_event_id}' and teamnum = {$teamnum}";
			if (debug()) print "<br>DEBUG-matchteameval: " . $query . "<br>\n";
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
	$editURL = "/matchteameval.php?teamnum={$teamnum}&type={$type}&matchnum={$matchnum}";



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
  print teamhref($teamnum) . "{$teamnum}";
  if (in_array($teamnum, $teams_need_eval)) print "&bull;";
  print " - {$teamname}</a>";
  if ($teamnickname) print "({$teamnickname})";
  print "</H2>\n";


  // frame top commands and match info in layout table
  print "<table valalign=\"top\">\n<tr valign=\"top\">\n<td>\n";

  // next and prev buttons
  // see if previous match exists and display buttong
  $matchnum_text = $matchnum - 1;
  $query = "select matchnum from match_instance where event_id = '{$sys_event_id}' and type = '{$type}' and matchnum = {$matchnum_text}";
  if (debug()) print "<br>DEBUG-matchteameval: " . $query . "<br>\n";
  if (!($result = @ mysqli_query ($connection, $query)))
    dbshowerror($connection);

  // get row
  if ($row = mysqli_fetch_array($result))
 	print "<a href=\"/matchteameval.php?type={$type}&matchnum={$matchnum_text}\">&lt Prev</a> &nbsp;&nbsp;&nbsp;";

  // see if next match exists and display
  $matchnum_text = $matchnum + 1;
  $query = "select matchnum from match_instance where event_id = '{$sys_event_id}' and type = '{$type}' and matchnum = {$matchnum_text}";
  if (debug()) print "<br>DEBUG-matchteameval: " . $query . "<br>\n";
  if (!($result = @ mysqli_query ($connection, $query)))
    dbshowerror($connection);

  // get row
  if ($row = mysqli_fetch_array($result))
 	print "<a href=\"/matchteameval.php?type={$type}&matchnum={$matchnum_text}\">Next &gt</a> &nbsp;&nbsp;";
	print "<br>";


  // view match details
  print "<a href=\"/matcheval.php?final={$final}&type={$matchidentifiers["type"]}"
         . "&matchnum={$matchidentifiers["matchnum"]}\">View Match Details</a><br>";

  // view
  print "<a href=\"/matchlist.php?final={$final}\">Match List</a><br>";
  print "<a href=\"/matchlist.php?final={$final}&highlight={$teamnum}\">View in match list</a><br><br>";

  if ($edit)
	{
		// if in edit mode, signal save with edit=2
		print "<form method=\"POST\" action=\"/matchteameval.php?edit=2&teamnum={$teamnum}"
			. "&type={$matchidentifiers["type"]}&matchnum={$matchidentifiers["matchnum"]}\">\n";
	}

  //
  // close first cell and space between next layout
  print "\n</td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>\n";

	//print teams in match block
	print "<table border=\"1\"><tr>Teams in Match:</tr><tr>";
	//prep for displaying colors for the teams
	//query to get color codes for teams
	$detail_query = "select type, matchnum, teamnum, color from match_team where "
		. $match_sql_identifier
		. " order by color DESC, matchnum";

    if (debug()) print "<br>DEBUG-matchteameval: " . $detail_query . "<br>\n";
	if (!($detail = @ mysqli_query ($connection, $detail_query )))
		dbshowerror($connection);

	//create array of upcoming teams
	$query = "select  a.type, a.matchnum, b.teamnum from match_team a, match_team b
		where a.event_id=b.event_id and a.type=b.type and a.matchnum=b.matchnum and a.color=b.color and
		a.event_id = '{$sys_event_id}' and a.teamnum='{$host_teamnum}' group by teamnum order by teamnum,  matchnum";
	if (debug()) print "<br>DEBUG-matchteameval: " . $query . "<br>\n";

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
		a.event_id = '{$sys_event_id}' and b.event_id = '{$sys_event_id}' and
		a.teamnum={$host_teamnum} group by teamnum order by teamnum,  matchnum";
    if (debug()) print "<br>DEBUG-matchteameval: " . $query . "<br>\n";

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
		// set teamnumT -- the teamnum local to the match block, not to whole form
		$teamnumT = $detailrow['teamnum'];

		// start output of individual cell
		print "<td";

		// the the host team, mark with color
		if ( $teamnumT == $host_teamnum)
			print " style=\"background-color: {$lyellow}\" ";

		// otherwise check whether we're playing with or against them, and the right type
		if (array_key_exists($teamnumT, $upcoming) && ($detailrow['type'] == $upcoming[$teamnumT]['type']))
			// if playing agaist and with, then blue
			if (($detailrow['matchnum'] < $upcoming[$teamnumT]['with_matchnum']) &&
				($detailrow['matchnum'] < $upcoming[$teamnumT]['against_matchnum']))
				print " style=\"background-color: {$lblue}\" ";
			// else if with
			else if ($detailrow['matchnum'] < $upcoming[$teamnumT]['with_matchnum'])
				print " style=\"background-color: {$lgreen}\" ";
			else if ($detailrow['matchnum'] < $upcoming[$teamnumT]['against_matchnum'])
				print " style=\"background-color: {$lred}\" ";

        // set display of teamnum with or without bullet
        if (in_array($teamnumT, $teams_need_eval)) $dispteamnum = $teamnumT . "&bull;"; else $dispteamnum = $teamnumT;

		if($teamnum == $teamnumT)
			print "> <b>{$row["color"]}{$dispteamnum}{$editor}</td>";
		else
			print ">{$row["color"]} <a href=\"/matchteameval.php?teamnum={$teamnumT}&event_id={$matchidentifiers["event_id"]}&
					type={$matchidentifiers["type"]}&matchnum={$matchidentifiers["matchnum"]}\">{$dispteamnum}{$editor}</a></td>";

		$counter++;
		if($counter==3)
			print "</tr><tr><td>Blue</td>";
	}


	print "</tr></table>\n";

	// Match info block
    print "\n</td><td>&nbsp;&nbsp;</td><td>\n";

	print "
	Match Info:
	<!---General Match Info Display--->
	<table valign=\"top\" border=1>
	";  // end of print

	$query = "select event_id, type, matchnum, scheduled_utime, actual_utime
		from match_instance where ".$match_sql_identifier;
	if (debug()) print "<br>DEBUG-matchteameval: " . $query . "<br>\n";

	if (! ($result = @ mysqli_query ($connection, $query) ))
		dbshowerror($connection, "die");
	if (! ($resultR = @ mysqli_query ($connection, "select score from match_instance_alliance where {$match_sql_identifier} and color='R'") ))
		dbshowerror($connection, "die");
	if (! ($resultB = @ mysqli_query ($connection, "select score from match_instance_alliance where {$match_sql_identifier} and color='B'") ))
		dbshowerror($connection, "die");

	$row = mysqli_fetch_array($result);
	$pointsR = mysqli_fetch_array($resultR);
	$pointsB = mysqli_fetch_array($resultB);

    //
	// time calculations to set up display
	//  - includes formatting scheduled time
	//  - feeding actual_utime to determine estimated time
	//  - if estimated and we can set, include a set button
	//      - processing for this case happens in the top of the page
	//
	// format scheduled time
    if ($row['scheduled_utime'] != NULL) $scheduled_display = date('H:i',$row['scheduled_utime']); else $row['scheduled_utime'];
    // get publishing info on actual time / estimated time
    $time_array = match_get_act_est_time($matchidentifiers["type"], $matchidentifiers["matchnum"], $row['actual_utime'], $row['scheduled_utime']);


	//print match data
	print "<tr><td>Event</td><td>Type</td><td>Match</td><td>Sched Time</td><td>{$time_array['heading_tag']} Time</td><td>Red Points</td><td>Blue Points</td></tr>";
	print "<tr><td>".$row["event_id"]."</td><td>".$row["type"]."</td><td>".$row["matchnum"]."</td>\n";


    // time presentation, from set up above
    //
    //
	print "<td>{$scheduled_display}</td>";

    if (($time_array['can_set']) && (! ($edit)))
      print "<form method=\"POST\" action=\"/matchteameval.php?teamnum={$teamnum}&type={$type}&matchnum={$matchnum}\">\n";

  	// print value
  	print "<td>{$time_array['display_time']} ";

    if ($time_array['can_set'])
      print "<input type=\"submit\" name=\"op\" value=\"Set Time\">";

    // end cell
    print "</td>\n";

    if ($time_array['can_set']) print "</form>\n";

    //
    // end time display
    //

    print "<td>".$pointsR["score"]."</td><td>".$pointsB["score"]."</td></tr>";

print <<< EOF_EOF

<!--- end match info --->
</tr></table>

<!--- end top header --->
</td></tr></table>

EOF_EOF
; // end of print


// show edit block
print dblockshowedit($edit, $dblock,$editURL);

 // Return navigation
print "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"{$base}\">Return to Home</a>\n";



 // **********************************************************************************
 //
 // include team info form
 //

 //
 // get team details

  // add game-specific fields and stats columns
  foreach($RankFields as $rankfield)
    if ($rankfield['display'] != NULL ) $rankcolumns = $rankcolumns . $rankfield['column'] . ", ";

  // add stats columns to rankcolumns
  foreach($stats_columns as $statcolumn=>$statarray)
    $rankcolumns = $rankcolumns . $statcolumn . ", ";

  // get row info
    // get team details define result set
    $query="select ". $rankcolumns . fields_insert("nameonly",NULL,$table_teambot) . " from teambot where event_id = '{$sys_event_id}' and teamnum = {$teamnum}";
    if (debug()) print "<br>DEBUG-matchteameval,teamdata: " . $query . "<br>\n";

    if (!($result = @ mysqli_query ($connection,$query)))
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
  	if (debug()) print "<br>DEBUG-matchteameval: " . $query . "<br>\n";

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