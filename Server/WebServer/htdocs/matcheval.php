<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - Evaluate Match
  //

    require "page.inc";
	pheader("Evaluate Match");
	$connection = dbsetup();

	$edit=$_GET["edit"];
	$final=$_GET["final"];
	$matchidentifiers = fields_load("GET", array("type", "matchnum"));


	$alliance_data = array("color", "score", "raw_points", "penalty_points");

	$match_sql_identifier = "event_id = '{$sys_event_id}' and type = '{$matchidentifiers["type"]}'
		and matchnum = {$matchidentifiers["matchnum"]}";

	$dblock = array(table=>"match_instance",where=>$match_sql_identifier);

	$color_names = array(R=>"Red", B=>"Blue");

    // set up for needs eval flag
    if (test_user_prop("needeval")) $teams_need_eval = allteams_need_eval();


	// handle update if returning from edit mode
	if ($edit == 2)
	{

	  	// load operation
	  	if ( $_POST[op] == "Save" )
		{
			// check row
			dblock($dblock,"changedby");
			dblock($dblock,"check");

			$table_team = array("score", "raw_points", "penalty_points");
			$formfields = fields_load("post", $table_team);

			foreach(array('R', 'B') as $color_initial)
			{
				// set opposite team color
				if ($color_initial == 'R') $color_opposite='B'; else $color_opposite='R';

				$query = "update match_instance_alliance set";
				foreach($table_team as $temp=>$tag)
				{
					$data_string = $_POST[$tag.$color_initial];
					if(!($data_string))
						$data_string=0;
					$query = $query." {$tag}={$data_string},";
				}

				$score=$_POST["raw_points".$color_initial]-$_POST["penalty_points".$color_initial];
				$query = $query." score={$score},";

				$s_points = seedscore($_POST["raw_points".$color_initial], $_POST["raw_points".$color_opposite],
					$_POST["penalty_points".$color_initial], $_POST["penalty_points".$color_opposite],
					$_POST["other_points".$color_initial], $_POST["other_points".$color_opposite]);//retrieve seed points

				$query = $query." seed_points={$s_points} where {$match_sql_identifier} and color='{$color_initial}'";

				// process query on seed points
				if (debug()) print "<br>DEBUG-matcheval: " . $query . "<br>\n";
				if (! (@mysqli_query ($connection, $query) ))
					dbshowerror($connection, "die");
			  } // end of foreach

			  // commit
			  if (! (@mysqli_commit($connection) ))
					dbshowerror($connection, "die");

		} // end of if Save


	  	// abandon row
		dblock($dblock,"abandon");

		// update completed
		$edit = 0;
	}

	// define lock phrase array
	// lock tables if in edit mode
	if ($edit) dblock($dblock,"lock");  // lock row with current user id

	//
	// top of page
	//

/*
// return if edit needed -- JLV
	if ($edit)
	{
		// if in edit mode, signal save with edit=2
		print "<form method=\"POST\" action=\"/matcheval.php?edit=3&final={$final}"
		    . "&type={$matchidentifiers["type"]}&matchnum={$matchidentifiers["matchnum"]}\">\n";
	}

    $editURL = "/matcheval.php?&final={$final}&type={$matchidentifiers["type"]}&matchnum={$matchidentifiers["matchnum"]}";
    print dblockshowedit($edit, $dblock, $editURL) . "\n";


	print "&nbsp;&nbsp;&nbsp;\n"
*/
	print "<a href=\"/matchlist.php?final={$final}\">Match List</a>\n";

	// return home
	print "&nbsp;&nbsp;&nbsp; <a href=\"{$base}\">Return to Home</a>\n";
	print "<br><br>\n";


	//
	// match info block
	//

	$query = "select event_id, type, matchnum, scheduled_utime, actual_utime
		from match_instance where ".$match_sql_identifier;

	if (! ($result = @ mysqli_query ($connection, $query) ))
		dbshowerror($connection, "die");

	$row = mysqli_fetch_array($result);

    // enclosing formating table
    print "<table border=\"0\"><tr><td>\n";

	// first table with match info
	print "<table valign=\"top\" border=1>\n";

	//print match data
	print "<tr><td>Type</td><td>Match Number</td><td>Sched Time</td><td>Actual Time</td></tr>";
	print "<tr><td>".$row["type"]."</td><td>".$row["matchnum"]."</td>\n";

    // time presentation
    $now = time();
    $sched = $row['scheduled_utime'];

    if ($sched != NULL) $display_sched = date('H:i',$sched); else $display_sched="";

	print "<td>{$display_sched}</td><td>" . substr($row["actual_time"],0,5) . "</td>\n";

	// end match display table
	print "</tr></table>\n";

/*
    // add button for time set
    print "
  	  <td valign=\"bottom\">
  	  <form method=\"POST\" action=\"/matcheval.php?/matcheval.php?edit=2&final={$final}&
			type={$matchidentifiers["type"]}&matchnum={$matchidentifiers["matchnum"]}\">
  	  <input type=\"submit\" name=\"op\" value=\"Set Match Time\">
  	  </form>
  	  </td>
    ";


    // end formatting table
    print "</tr></table>\n";
*/


//			$row["scheduled_time"]."</td><td>".$row["actual_time"]."</td></tr>";


	//print teams in the match
	print "<br><table border=1><tr><b>Teams:</b></tr>\n<tr>";//<td>Red</td>;

	//prep for displaying colors for the teams
	//query to get color codes for teams
	$detail_query = "select type, matchnum, teamnum, color from match_team"
	    . " where $match_sql_identifier "
	    . " order by color DESC, matchnum";
	if (debug()) print "<br>DEBUG-matcheval: " . $detail_query . "<br>\n";

	if (!($detail = @ mysqli_query ($connection, $detail_query )))
		dbshowerror($connection);

	//create array of upcoming teams
	$query = "select  a.type, a.matchnum, b.teamnum from match_team a, match_team b
		where a.event_id = '{$sys_event_id}' and a.type=b.type and a.matchnum=b.matchnum and a.color=b.color and
		a.teamnum='{$host_teamnum}' group by teamnum order by teamnum,  matchnum";
	   if (debug()) print "<br>DEBUG-matcheval: " . $query . "<br>\n";

	if (!($result = @ mysqli_query ($connection, $query)))
		dbshowerror($connection);
	while ($row = mysqli_fetch_array($result))
	{
		$upcoming[$row['teamnum']]['type'] = $row['type'];
		$upcoming[$row['teamnum']]['with_matchnum'] = $row['matchnum'];
	}

	// load teams we are playing against
	$query = "select  a.type, a.matchnum, b.teamnum from match_team a, match_team b
		where a.event_id = '{$sys_event_id}' and a.type=b.type and a.matchnum=b.matchnum and a.color!=b.color and
		a.teamnum='{$host_teamnum}' group by teamnum order by teamnum,  matchnum";
	if (debug()) print "<br>DEBUG-matcheval: " . $query . "<br>\n";

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
		// set teamnum
		$teamnum = $detailrow['teamnum'];

		// start output of individual cell
		print "<td";

	    // the the host team, mark with color
	    if ( $teamnum == $host_teamnum)
			print " style=\"background-color: {$lyellow}\" ";
		// otherwise check whether we're playing with or against them, and the right type
		else if (array_key_exists($teamnum, $upcoming) && ($detailrow['type'] == $upcoming[$teamnum]['type']))
			// if playing agaist and with, then blue
			if (($detailrow['matchnum'] < $upcoming[$teamnum]['with_matchnum']) &&
				($detailrow['matchnum'] < $upcoming[$teamnum]['against_matchnum']))
				print " style=\"background-color: {$lblue}\" ";
			// else if with
			else if ($detailrow['matchnum'] < $upcoming[$teamnum]['with_matchnum'])
				print " style=\"background-color: {$lgreen}\" ";
			else if ($detailrow['matchnum'] < $upcoming[$teamnum]['against_matchnum'])
				print " style=\"background-color: {$lred}\" ";

		print ">{$row["color"]} <a href=\"/matchteameval.php?teamnum={$teamnum}"
			        . "&type={$matchidentifiers["type"]}&matchnum={$matchidentifiers["matchnum"]}\">{$teamnum}";
        // print needs eval bullet
        if (in_array($teamnum, $teams_need_eval)) print "&bull;";
        print "{$editor}</a></td>\n";

		$counter++;
		if($counter==3)
			print "</tr><tr><td>Blue</td>";
	}

	print "</tr></table>
	<br>
	"; // end of print


	print "<table><tr valign=\"top\">";
	$options["tr"] = 1;  // add tr tags


    //
    // show scores
    //

    // build display and query fields
    //   Display fields are a key-value array with columnname=>displaytext
    //
    // start with standard fields
    $fields = array("score"=>"Score");

    // loop through array
    foreach($ScoreFields as $element=>$scorefield)
      $fields = array_merge($fields, array("f_score{$element}" => $scorefield['display']));

    // form query
    $query = "select " . fields_insert("nameonly", $fields, "") . " from match_instance_alliance where "
              . $match_sql_identifier . " and color=";

    // get each row from db
	foreach(array('R', 'B') as $color_d)
	{
	  if (debug()) print "<br>DEBUG-matcheval: " . $query . "<br>\n";
      if (!($result = @ mysqli_query ($connection, $query . "'{$color_d}'")))
			dbshowerror($connection);

      $srow[$color_d] = mysqli_fetch_array($result);
    }

    // format into table then loop through score variables

    print "<td><table border=1>\n<tr><th>Red</th><th>Score Breakout</th><th>Blue</th></tr>\n";

    // loop through params
    foreach($fields as $column=>$display)
      print "<tr><td>{$srow['R'][$column]}</td><td>$display</td><td>{$srow['B'][$column]}</td></tr>\n";

    print "</table>\n";


/*
	//$data_tag = array(score=>"Score", raw_points=>"Raw Points", penalty_points=>"Penalty Points");
	$data_tag = array(raw_points=>"Raw Points", penalty_points=>"Penalty Points", other_points=>"Other Points");

	foreach(array('R', 'B') as $color_initial)
	{
		if (!($result = @ mysqli_query ($connection,
			"select color, score, raw_points, penalty_points, seed_points from match_instance_alliance where ".
				$match_sql_identifier . " and color='{$color_initial}'")))
			dbshowerror($connection);
		if (debug()) print "<br>DEBUG-matcheval: " . $query . "<br>\n";

		while($row = mysqli_fetch_array($result))
		{
			print "<td><table border=1><tr><td><b>{$color_names[$color_initial]}</b></td>";

			if($row["score"])
				print "<tr><td>Score</td><td>{$row["score"]}</td></tr>";
			else
				print "<tr><td>Score</td><td>None</td></tr>";

			foreach($data_tag as $tag=>$data_name)
			{
				print "<tr><td>{$data_name}</td><td>";
				if($edit)
				{
					print "<input type=\"text\" name=\"{$tag}{$color_initial}\" maxlength=2 value=\"{$row[$tag]}\"></td></tr>";
				}
				else
				{
					if($row[$tag] != "")
						print "{$row[$tag]}</td></tr>";
					else
						print "Not Entered</td></tr>";
				}
			}

			// seed points
			if($row["seed_points"])
				print "<tr><td>Seed Points</td><td>{$row["seed_points"]}</td></tr>";
			else
				print "<tr><td>Seed Points</td><td>None</td></tr>";
			print "</table></td>";
		}
	}

*/

	print "</tr></table>";

    // add edit link or submit button
	print "<br>\n";
    print dblockshowedit($edit, $dblock, $editURL) . "\n";

  // close the form if in edit mode
  if ($edit) print "\n</form>\n";


  print "<br><a href=\"/matchlist.php?final={$final}\">Match List</a>\n";

	showupdatedby($dblock);

	pfooter();
?>
