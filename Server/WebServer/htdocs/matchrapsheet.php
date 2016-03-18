<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - Match Rap Sheet
  //
  // Shows competitive data for match
  //

	require "page.inc";

	// load paramters
	$long=$_GET["long"];	     // indicates "long form with all match listing on teams
	$public=$_GET["public"]; 	// sharable version for other teams in alliance

    // load variables
	$matchidentifiers = fields_load("GET", array("event_id", "type", "matchnum"));
	$match_sql_identifier = "event_id = '{$matchidentifiers["event_id"]}' and type = '{$matchidentifiers["type"]}'
		and matchnum = {$matchidentifiers["matchnum"]}";

	// determine header
	$header = $host_team_name . " - Match Rap Sheet ";
	if (! ($public)) $header = $header . "(Private)";
	$header = $header .  " {$matchidentifiers["type"]}-{$matchidentifiers["matchnum"]}";
	pheader($header);
	$connection = dbsetup();

  // table data
  $table_teambot = array_merge ( array("rank_overall","rating_overall","rating_overall_off","rating_overall_def",
  	"rank_pos1","rating_pos1","rank_pos2","rating_pos2","rank_pos3","rating_pos3","offense_analysis",
  	"defense_analysis","pos1_analysis","pos2_analysis","pos3_analysis","robot_analysis","driver_analysis",
  	"with_recommendation","against_recommendation"),
  	param_array("Play"), param_array("Pit"));

	$team_rank_fields = array(rank_overall=>"Overall Rank", rating_overall=>"Overall Rating (0-9)",
		rating_overall_off=>"Offensive Rating (0-9)", rating_overall_def=>"Defensive Rating (0-9)");
	// if fields positions matter, add positions
	if ($field_positions === TRUE)
		$team_rank_fields = array_merge ( $team_rank_fields,
		  array ( rank_pos1=>"Position 1 Rank", rating_pos1=>"Position 1 Rating", rank_pos2=>"Position 2 Rank",
		  rating_pos2=>"Position 2 Rating", rank_pos3=>"Position 3 Rank", rating_pos3=>"Position 3 Rating" )
		);

	$eval_with_fields = array(
		with_recommendation=>"With Recommendation",
		offense_analysis=>"Offense Analysis",
		defense_analysis=>"Defense Analysis",
		robot_analysis=>"Overall Robot Analysis",
		driver_analysis=>"Driver Analysis"
		);
    // if fields positions matter, add positions
	if ($field_positions === TRUE)
      $eval_with_fields = array_merge ( $eval_with_fields,
		array ( pos1_analysis=>"Position 1 Analysis",
		pos2_analysis=>"Position 2 Analysis",
		pos3_analysis=>"Position 3 Analysis")
		);

	$eval_against_fields = array(
		against_recommendation=>"Against Recommendation",
		offense_analysis=>"Offense Analysis",
		defense_analysis=>"Defense Analysis",
		robot_analysis=>"Overall Robot Analysis",
		driver_analysis=>"Driver Analysis"
		);
	// if fields positions matter, add positions
	if ($field_positions === TRUE)
      $eval_against_fields = array_merge ( $eval_with_fields,
		array ( pos1_analysis=>"Position 1 Analysis",
		pos2_analysis=>"Position 2 Analysis",
		pos3_analysis=>"Position 3 Analysis")
		);

	// get our team
	$query = "select teamnum, color from match_team where {$match_sql_identifier} and teamnum = {$host_teamnum}";
	if (! ($result = @ mysqli_query ($connection, $query)))
		dbshowerror($connection, "die");

	$us= mysqli_fetch_array($result);

	// determine with color
	if ($us["color"] == "R")
	{
		$with_color="R";
		$with_color_long="Red";
		$against_color="B";
		$against_color_long="Blue";
		$order="ASC";
	}
	else
	{
		$with_color="B";
		$with_color_long="Blue";
		$against_color="R";
		$against_color_long="Red";
		$order="DESC";
	}


    // get teams in match
    $query="select teambot.teamnum, match_team.color color, name, nickname ". fields_insert("nameonly",NULL,$table_teambot)
      . " from match_team, teambot, team where match_team.event_id = '{$def_event_id}' and teambot.event_id = '{$def_event_id}' "
      . " and match_team.teamnum=teambot.teamnum and match_team.teamnum=team.teamnum"
      . " and match_team.teamnum != {$host_teamnum} and {$match_sql_identifier} order by match_team.color {$order}, match_team.teamnum";

	if (! ($result = @ mysqli_query ($connection, $query)))
  		dbshowerror($connection, "die");

	// load teams
  	$cnt=0;
  	while($row = mysqli_fetch_array($result))
		$team[$cnt++]=$row;
	// set teamcnt so that arrays can work with 5 or 6 teams
	$teamcnt = $cnt;

    // create default table header with teams
    $tablehead = "<th></th><th>{$against_color_long} {$team[0]["teamnum"]}</th>"
       . "<th>{$against_color_long} {$team[1]["teamnum"]}</th>"
       . "<th>{$against_color_long} {$team[2]["teamnum"]}</th>";

       // if public, don't include our alliance data
       if (! ($public))
         $tablehead = $tablehead
       . "<th>{$with_color_long} {$team[3]["teamnum"]}</th>"
       . "<th>{$with_color_long} {$team[4]["teamnum"]}</th>";
       if ($teamcnt == 6) $tablehead = $tablehead . "<th>{$with_color_long} {$team[5]["teamnum"]}</th>";

	// return home
	print "<a href=\"/\">Return to Home</a>\n";
	print "&nbsp;&nbsp;&nbsp; <a href=\"/matchlist.php?final={$final}\">Match List</a>\n";
	print "<br>";


    // format overall sheet and first table
    print "
    <!--- format over table --->
    <table valign=\"top\"><tr valign=\"top\"><td>\n

    <!--- format results table --->
    <table border=\"2\" valign=\"top\">
    <tr>{$tablehead}</tr>
    ";

	// loop through data in first fields and populate
	foreach ( $team_rank_fields as $fieldname => $field_desc)
	{

		// start row
		print "<tr><td>{$field_desc}</td>\n";

		// loop through teams
		// if public, set to 3 (competition) vs $teamcnt for all
		if ($public) $tot=3; else $tot=$teamcnt;
		for($i=0; $i<$tot; $i++)
			print "<td>{$team[$i][$fieldname]}</td>";

	 	//end row
		print "</tr>\n";
	}
	// end data table
	print "</table></td>\n";

	// end of first row of data tables
    print "</tr><tr valign=\"top\"><td>\n";

	// loop through Play Field data

    // format overall sheet and first table
    print "
    <!--- format over table --->
    <b>Field Data</b>
    <table valign=\"top\"><tr><td>\n

    <!--- format results table --->
    <table border=\"2\" valign=\"top\">
    <tr>{$tablehead}</tr>
    ";

    print param_report ($team, "Play", $public, $teamcnt);

	print "</table></td></tr><tr><td>\n";


	print "</td></tr>\n";
    // end data grids
    print "</table></tr></table>\n";



    //
    // print comparatives
    //

	// for competition first
	print "<hr>\n";
	print "<h2>Competition Briefs</h2>\n";

    // loop through teams
    for($i=0; $i<3; $i++)
    {
        // team heading
        print "<tr><td><h3>Team {$team[$i]["teamnum"]} - {$team[$i]["name"]}";
        // if nickname, print too
        if ($team[$i]["nickname"]) print " ({$team[$i]["nickname"]})";
        print "</h3>\n<table border=\"2\" valign=\"top\">";


        // loop through data in first fields and populate
	    foreach ( $eval_against_fields as $fieldname => $field_desc)
	   		print "<tr valign=\"top\"><td>{$field_desc}</td><td>{$team[$i][$fieldname]}</td></tr>\n";

		// finish table
		print "</table>\n";
	}


	// partner alliance next -- only if not public
	if (! ($public))
	{
		print "<hr>\n";
		print "<h2>Cooperation Briefs</h2>\n";

        // loop through other teams  (3 or teamcnt)
		for($i=3; $i<$teamcnt; $i++)
		{
			// team heading
			print "<tr><td><h3>Team {$team[$i]["teamnum"]} - {$team[$i]["name"]}";
			// if nickname, print too
			if ($team[$i]["nickname"]) print " ({$team[$i]["nickname"]})";
			print "</h3>\n<table border=\"2\" valign=\"top\">";


			// loop through data in first fields and populate
			foreach ( $eval_with_fields as $fieldname => $field_desc)
				print "<tr valign=\"top\"><td>{$field_desc}</td><td>{$team[$i][$fieldname]}</td></tr>\n";

			// finish table
			print "</table>\n";
		}
	}

?>


<?php
  //
  // *************************************************************************
  //
  // Added team match evaluation data on the end of each match
  //
  // Code essentially copied from teammatches.php
  //


// if long form
if ($long)
{

  require "fieldnames.inc";

	// if fields positions matter, start with position
	if ($field_positions === TRUE)
		$field_array = array("position");
	else
		$field_array = array();

	// define fields used
	$field_array = array_merge ($field_array, array("rating_offense", "rating_defense", "raw_points",
		"penalties","match_offense_analysis", "match_defense_analysis","match_pos_analysis",
		"match_with_recommendation","match_against_recommendation"),
  		param_array("Match"));

  // print large spacer
  print "
  <b><hr width=\"200%\"></b>
  <b><hr width=\"200%\"></b>
  <h1>Match details for each team</h1>
  ";  // end of print

//
// each team
//
//
//  for each team, print out all matches

// if public, only print first 3, not competition
if ($public) $tot=3; else $tot=$teamcnt;
for($i=0; $i<$tot; $i++)
 {
  // set team, and let it go from there
  $teamnum=$team[$i]["teamnum"];

  // announce team
  print "<hr width=\"200%\">\n";
  print "<hr width=\"200%\">\n";

  // team heading
  print "<h2>Team {$team[$i]["teamnum"]} - {$team[$i]["name"]}";
  // if nickname, print too
  if ($team[$i]["nickname"]) print " ({$team[$i]["nickname"]})";
  print "</h2>\n";



 //
 // top of loop
 //

 $query =
     "select event_id, type, matchnum from match_team where teamnum = {$teamnum} order by type, matchnum";

 if (! ($matches_result = @ mysqli_query ($connection, $query) ))
		dbshowerror($connection, "die");

 while ($matches_row = mysqli_fetch_array($matches_result) )
 {
  $matchidentifiers = array ("event_id"=>$matches_row["event_id"], "type"=>$matches_row["type"],
    	"matchnum"=>$matches_row["matchnum"]);
  //$event_id = $matches_row["event_id"];
  //$type = $matches_row["type"];
  //$matchnum = $matches_row["matchnum"];


  // set up variables for this run
	// $matchidentifiers = fields_load("GET", array("event_id", "type", "matchnum", "teamnum"));

	$match_sql_identifier =
		"event_id = '{$matchidentifiers["event_id"]}' and type = '{$matchidentifiers["type"]}'
		and matchnum = {$matchidentifiers["matchnum"]}";
	$team_sql_identifier = "teamnum={$teamnum}";




  // top of match listing
  print "<hr>\n";

  print "
	<!---- Table layout ---->
	<table valign=\"top\">
	<tr valign=\"top\"><td>

	<table valign=\"top\">
	<tr valign=\"top\"><td>

	<!---General Match Info Display--->
	<table valign=\"top\" border=1>
  ";

  // event_id

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
		print "<tr><th>Leg</th><th>Type</th><th>Match</th><th>Sched</th><th>Actual</th><th>Red</th><th>Blue</th></tr>";
		print "<tr><td>".$row["event_id"]."</td><td>".$row["type"]."</td><td>".$row["matchnum"]."</td><td>".
			$row["scheduled_time"]."</td><td>".$row["actual_time"]."</td><td>".$pointsR["score"]."</td><td>".$pointsB["score"]."</td></tr>";


		//print teams
		$color_names = array(R=>"Red", B=>"Blue");
		print "<table border=1><tr><b>Teams:</b></tr><tr>";//<td>Red</td>;

		foreach(array('R', 'B') as $color_initial)
		{
			print "<td>{$color_names[$color_initial]}</td>";
			if (! ($result = @ mysqli_query ($connection, "select teamnum from match_team where ".$match_sql_identifier." and color='{$color_initial}'") ))
				dbshowerror($connection, "die");
			while($row = mysqli_fetch_array($result))
			{
				if($row["teamnum"]==$teamnum)
					print "<td>{$row["color"]} {$row["teamnum"]}</td>";
				else
					print "<td>{$row["color"]} <a href=\"/matchteameval.php?teamnum={$row["teamnum"]}&event_id={$matchidentifiers["event_id"]}&
						type={$matchidentifiers["type"]}&matchnum={$matchidentifiers["matchnum"]}\">{$row["teamnum"]}</a></td>";
			}
			print"</tr><tr>";
		}
		print "</tr></table>";

	print "
	<!---Individual Team Evaluation--->
	";

  	$query = "select ". fields_insert("nameonly",NULL,$field_array)
  		. " from match_team where {$match_sql_identifier} and {$team_sql_identifier}";

	if (! ($result = @ mysqli_query ($connection, $query)))
		dbshowerror($connection, "die");
	$row = mysqli_fetch_array($result);

	// page break
	print "</td><td>";

	// start table
	print "<table border=2>";

	$options["tr"] = 1;  // add tr tags
	print fill_tab_text_field($edit, $options, $row, $match_team_name, $match_team_size);

	print "</table>";

	print "
	<!--- table layout to other column --->
	</td><td>&nbsp;&nbsp;</td><td>
	";

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
	  $options["pagebreak"]=2;
	  $options["pagebreakstring"]="\n</table>\n</td><td>\n<table border=\"1\" valign=\"top\">";
	  print tabparamfields($edit, $options, $row, "Match");

  // end blocks of data, table layout
  print "\n</table>
    </td></tr></table>
    </td></tr></table>";

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
  print "</td></tr></table></table>\n";


 // end of multi match loop
 }

 // team for loop
 }
// end of long
}


// return home
print "<br><br>\n";
print "<a href=\"/\">Return to Home</a>\n";
print "&nbsp;&nbsp;&nbsp; <a href=\"/matchlist.php?final={$final}\">Match List</a>\n";


?>







<?php
	pfooter();
?>