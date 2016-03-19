<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - List Matches
  //
  require "page.inc";
  pheader("Competition System Match Listings");
  $connection = dbsetup();

  // get final flag
  $final=$_GET["final"];
  if(isset($_POST["highlight"]))
  	$highlight=$_POST["highlight"];
  else
  	$highlight=$_GET["highlight"];

  //
  // data preparation -- set up the variables

  // load "upcoming" matches
  //   array is keyed by teamnum and includes type, with_matchnum, against_matchnum

  // first load teams we are playing with
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

  //
  // Determine query scope and set per-user preferences
  //  Basic idea is to go to the last query filter the user generated, when referred from some other place
  //   in the systems.  This functionality gets rid of the need for explicitly set preference in this
  //   specific area.  If the system can remember the last state, we don't need the user complexity of setting
  //
  //
  //  Key:
  //   - if filter is not set, assume user wants the previously used query
  //
  //  Note: there is a case where a value won't be set in the user table.  It's a case where one starts in the
  //    matchlist form, instead of going there from outside the form.  We're not going to deal with the case
  //    because it will very rarely if ever happens, and it if does, the functionality of the user preference
  //    isn't that difficult.  I.e. We're not dealing with thie case.
  //

  // set filter.
  //   If variable not set, use last variable from database, otherwise the variable and set in database
  if (! (isset($_GET['filter'])))
  {
 	// query db
    if (!($result = @ mysqli_query ($connection,"select matchview from user_profile where user = '{$user}'")))
	    dbshowerror($connection);
	// if row is set, use row.  If nothing set for user, assume "all" for the value and insert new user
  	if ($row = mysqli_fetch_array($result))
  		$filter = $row['matchview'];
  	else
  	{
		$filter = "A";
		if (!($result = @ mysqli_query ($connection,
		  		"insert into user_profile (user) values ('{$user}')")))
	    	dbshowerror($connection);
	}
  }
  else
  {
  	// get filter as set by URL line
  	$filter = $_GET['filter'];
  }

  // write preference to database, and commit
  if (!($result = @ mysqli_query ($connection,"update user_profile set matchview = '{$filter}'")))
	    dbshowerror($connection);
  if (! (@mysqli_commit($connection) ))
		dbshowerror($connection, "die");


  //
  // navigation and filters
  //

  // set up form *********************
	print "<form method=\"POST\" action=\"/matchlist.php?final={$final}\">\n";

  print "<a href=\"/\">Return to Home</a>\n";

  // display options
  //   If user has selected an option, show as bold with no link, otherwise, show as link option
  foreach(array("A"=>"All Matches","Q"=>"Qualification","F"=>"Finals","P"=>"Practice") as $type=>$desc)
  {
  	print "&nbsp;&nbsp; ";
  	if ($type == $filter)
  		print "<b>$desc</b>\n";
  	else
  		print "<a href=\"/matchlist.php?filter={$type}&highlight={$highlight}\">$desc</a>\n";
  }

  // print key
  print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
  print "&nbsp; Home team: <font style=\"background-color: {$lyellow}\"> &nbsp;&nbsp;&nbsp;&nbsp;</font>\n";
  print "&nbsp; With: <font style=\"background-color: {$lgreen}\"> &nbsp;&nbsp;&nbsp;&nbsp;</font>\n";
  print "&nbsp; Against: <font style=\"background-color: {$lred}\"> &nbsp;&nbsp;&nbsp;&nbsp;</font>\n";
  print "&nbsp; Both: <font style=\"background-color: {$lblue}\"> &nbsp;&nbsp;&nbsp;&nbsp;</font>\n";


  // Add entry field
		print "
		&nbsp; &nbsp;&nbsp;&nbsp;
		<input type=\"text\" name=\"highlight\" size=4 maxlength=4 value=\"{$highlight}\">
		<INPUT TYPE=\"submit\" name=\"Submit\" VALUE=\"HiLite\" ALIGN=middle BORDER=0>
		</form>";

  // set up teams section
  print <<< EOF_EOF
  <!--- Teams Section --->
  <table valign="top">

  <tr valign="top">
  <td>
  <table border="2">
EOF_EOF
; // end of print

  // set up table head section
//  $table_head = "<tr><th>Lg</th><th>Typ</th><th>Num</th>";
  $table_head = "<tr><th>T</th><th>#</th>";
  if ($final == 1)   $table_head = $table_head . "<th>Final</th>";
  $table_head = $table_head . "<th>Sched</th><th>Actual</th><th>Red1</th>
  	<th>Red2</th><th>Red3</th><th>Blue1</th><th>Blue2</th><th>Blue3</th><th>Rap</th></tr>\n";

  print $table_head;

  //  *************************************
  //
  // define count and data query
  $cquery = "select count(*) tot from match_instance ";
  $query = "select league, type, matchnum, final_type, scheduled_time, actual_time from match_instance ";
  $where = '';

  // set where clause
  if ($filter != "A") $where = " where type = '{$filter}' ";

  // finish query
  $query = $query . $where . "  order by league, type, matchnum";


  // get row count first for pagebreak
  if (!($result = @ mysqli_query ($connection, $cquery . $where)))
    dbshowerror($connection);
  $row = mysqli_fetch_array($result);
  $tot = $row['tot'];
  $pagebreak = ceil (($tot +.5) / 2);   	// ceil rounds up

  // get data set
  if (!($result = @ mysqli_query ($connection, $query)))
    dbshowerror($connection);

  $rowcnt=1;
  while ($row = mysqli_fetch_array($result))
   {
		// clear host_team_row
		$host_team_row = FALSE;

   		//find if this match contains the highlighting team
   		$bold="";
   		if($highlight)
   		{
			if (!($result2 = @ mysqli_query ($connection, "select teamnum from match_team where type = '{$row["type"]}' and matchnum = {$row["matchnum"]}")))
				dbshowerror($connection, die);
			while($row2 = mysqli_fetch_array($result2))
				if($row2["teamnum"]==$highlight)
					$bold="<b>";
   		}

		// print each row with href
		print "<tr>";
	//	print "<td><a href=\"/matcheval.php?final={$final}&league={$row["league"]}&type={$row["type"]}&matchnum={$row["matchnum"]}\">{$row["league"]}</a></td>\n";
		print "<td>{$bold}<a href=\"/matcheval.php?final={$final}&league={$row["league"]}&type={$row["type"]}&matchnum={$row["matchnum"]}\">{$row["type"]}</a></td>\n";
		print "<td>{$bold}<a href=\"/matcheval.php?final={$final}&league={$row["league"]}&type={$row["type"]}&matchnum={$row["matchnum"]}\">{$row["matchnum"]}</a></td>\n";
		if ($final == 1) print "<td>{$row["final_type"]}</td>";   // show final type only if set
		print "<td>" . substr($row["scheduled_time"],0,5) . "</td><td>" . substr($row["actual_time"],0,5) . "</td>\n";

		// get teams in red/blue order
		$detail_query = "select league, type, matchnum, teamnum, color from match_team"
		    . " where league = '{$row["league"]}' and type = '{$row["type"]}' and matchnum = {$row["matchnum"]} "
		    . " order by color DESC, matchnum";

		if (!($detail = @ mysqli_query ($connection, $detail_query )))
			dbshowerror($connection);

		$counter=0;
		$team_list="";
		$colorchar="r";
		while ($detailrow = mysqli_fetch_array($detail))
		{
			// set teamnum
			$teamnum = $detailrow['teamnum'];

			// start output of individual cell
			print "<td";

		    // the the host team, mark with color. Also set flag
		    if ( $teamnum == $host_teamnum)
		    	{
					print " style=\"background-color: {$lyellow}\" ";
					$host_team_row = TRUE;
				}

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

			// finish rest of URL
			print ">";
			if($detailrow["teamnum"]==$highlight)
				print "<b>";
			print "<a href=\"/matchteameval.php?final={$final}&teamnum={$detailrow["teamnum"]}&league={$row["league"]}&type={$row["type"]}&matchnum={$row["matchnum"]}\">{$detailrow["teamnum"]}</a></td>\n";


			$counter++;
			if($counter==4)
			{
				$colorchar="b";
				$counter=1;
			}
			$team_list=$team_list.$colorchar.$counter."=".$detailrow["teamnum"];
			if(!($colorchar=="b" && $counter==3))
				$team_list=$team_list."&";
		}

		//print "<td>{$team_list}</td>";

		// rap sheet links
		print "<td>";
		// regular rap
		print "<a href=\"/matchrapsheet.php?league={$row["league"]}&type={$row["type"]}&matchnum={$row["matchnum"]}\">Rap</a>";
		// long rap
		print " <a href=\"/matchrapsheet.php?league={$row["league"]}&type={$row["type"]}&matchnum={$row["matchnum"]}&long=1\">L</a>";

		// public rap, if on a host_team row
		if ($host_team_row === TRUE)
			print " <a href=\"/matchrapsheet.php?league={$row["league"]}&type={$row["type"]}&matchnum={$row["matchnum"]}&public=}\">P</a>";

		print "</td>";

		// end row
		print "</tr>\n";

    // if more than 30 rows, pagenate
    if (! ($rowcnt++  % $pagebreak  ))
        // end last table, move next cell, start another table
        print "</table></td><td><table border=\"2\">\n". $table_head;
    }
?>

</table>
</tr>
</table>



<?php
   pfooter();
 ?>
