<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - Rank teams page
  //
  // Single form to rank teams will overall rank, and if position-oriented, also rank by positions
  //  Allows sorting as part of overall analysis.
  //

  require "page.inc";

  // get variables
  $edit=$_GET["edit"];
  $sort=$_GET["sort"];		// field to be sorted
    // set default if needed
    if ($sort == "") $sort="rank_overall";

  // lsort -- listing sort  (lsort is added on from other more complicated sort associated with ranks
  $lsort=$_GET["lsort"];		// field to be sorted
    // set default if needed
    if ($lsort == "") $lsort="rank_overall";

  // order
  $order=$_GET["order"];

  // header and setup
  pheader($host_team_name . " - Ranking");
  $connection = dbsetup();

  // define lock array, fields arrays
  $dblock = array(table=>"process_lock",where=>"lock_id = 'ranking'");
  $table_team = array("name","nickname","org","location","students","website");

  //
  // define default column list that is always shown.
  //
  // custom columns are defined in params.inc, $rank_columns_custom
  //
  //  Format:
  //   key=>array, array of arrays that include:
  //     - key: db column name
  //     - array:
  //       - display text
  //       - format mask to be used in printf
  //       - order: a for ascending sort, d for descending
  //

  //  Note: does not include position functionality
  //  Format:
  //   Array of:
  //     - tag or column name
  //     - display text
  //     - format mask to be used in printf
  $rank_columns_always  = array (
     "rank_overall"=>array("heading"=>"Overall Rank", "order"=>"d"),
     "rating_overall"=>array("heading"=>"Overall Rating", "order"=>"d"),
     "rating_overall_off"=>array("heading"=>"Offense Rating", "order"=>"d"),
     "rating_overall_def"=>array("heading"=>"Defense Rating","order"=>"d")
     );

  // merge always and custom columns
  $rank_columns = array_merge ($rank_columns_always, $rank_columns_custom);

  //
  // doc from below
  //
  // add edit link or submit button
  //
  //  this part is a little tricky:
  //    instead of using the normal post field of op for the Save button, we set a different name for each
  //    place the button is pressed. Then the results processing logic scans through the post fields and determines
  //    how to set the sort variable.  The normal "Save" is stored as a hidden value that is overwritten by a cancel operation.
  //


  // handle update if returning from edit mode
  // edit:
  //   1 = go into edit mode
  //   2 = save and return to non-edit mode
  //   3 = save and stay in edit mode
  if (($edit == 2) || ($edit == 3))
  {
  	// load operation
  	if ( $_POST[op] == "Save")
	{
		// check db
		dblock($dblock,"check");

	    // query teams from db, then iterate load variables, sort, and store
		// query and load
		$query = "select teamnum from teambot where event_id = '{$sys_event_id}'";
		if (debug()) print "<br>DEBUG-rank: " . $query . "<br>\n";
		if (! ($result = @ mysqli_query ($connection, $query)))
  			dbshowerror($connection, "die");

		// load teams
  		while($row = mysqli_fetch_array($result))
  		{
  			$teamnum=$row["teamnum"];

			// load sort array
			$teamsrank[$teamnum]=$_POST["{$teamnum}_{$sort}"];
		}

        // sort ranks
        asort($teamsrank);

        // store in order processed, placing NULL's at end
        $cnt=1;
        foreach ($teamsrank as $teamnum => $rank)
        {
        	// if NULL skip, otherwise process
        	if ($rank != "")
        	{
        		$query = "update teambot set {$sort} = {$cnt} where event_id = '{$sys_event_id}' and teamnum = {$teamnum} ";
        		if (debug()) print "<br>DEBUG-rank: " . $query . "<br>\n";
        		if (! (@mysqli_query ($connection, $query) ))
					dbshowerror($connection, "die");

				$cnt = ++$cnt;
			}
		}

		// process nulls at end of list
        foreach ($teamsrank as $teamnum => $rank)
        {
        	// if NULL skip, otherwise process
        	if ($rank == "")
        	{
        		$query = "update teambot set {$sort} = {$cnt} where event_id = '{$sys_event_id}' and teamnum = {$teamnum} ";
        		if (debug()) print "<br>DEBUG-rank: " . $query . "<br>\n";
        		if (! (@mysqli_query ($connection, $query) ))
					dbshowerror($connection, "die");

				$cnt = ++$cnt;
			}
		}


		// commit
		if (! (@mysqli_commit($connection) ))
		  dbshowerror($connection, "die");

		//
		// see doc above
		//
		// look through submit vars and set sort mode.  If set, edit is 3
		if ( $_POST[overall_save] == "Save-Edit" ) {$sort="rank_overall"; $edit=3;}
		if ($_POST[pos1_save] == "Save-Edit") { $sort="pos1"; $edit=3;}
		if ($_POST[pos2_save] == "Save-Edit") { $sort="pos2"; $edit=3;}
		if ($_POST[pos3_save] == "Save-Edit") { $sort="pos3"; $edit=3;}
	}

	// abandon/cancel lock
	dblock($dblock,"abandon");

    // update completed
    if ($edit == 2) $edit = 0;
   }  // end of edit = 2

   // define lock phrase array
   // lock tables if in edit mode
   if ($edit) dblock($dblock,"lock");  // lock row with current user id

  //
  // loads rank values from a specifed type of ranking, allows the user to edit and resort the rankings
  //  overall, pos1, pos2, pos3

  //
  // load rank values
  //

  unset( $teamsrank);  // unsetting rank arry

  // build query
    // build columns list
    foreach($rank_columns as $column=>$col_def)
      $columns = $columns . $column . ", ";

    // order by
    if ($order == "d") $sql_ord='DESC'; else $sql_ord='ASC';
    if ($sort) $orderby = " order by isnull({$lsort}), {$lsort} {$sql_ord}, rank_overall, rating_overall, rating_overall_off, teamnum";

    $query = "select teambot.teamnum teamnum, name, nickname, {$columns} rank_pos1, rating_pos1, rank_pos2, rating_pos2,
  			rank_pos3, rating_pos3
  			from teambot, team where teambot.event_id = '{$sys_event_id}' and teambot.teamnum=team.teamnum {$orderby}";

	// query and load
	if (debug()) print "<br>DEBUG-rank: " . $query . "<br>\n";
	if (! ($result = @ mysqli_query ($connection, $query)))
  		dbshowerror($connection, "die");

	// load teams
  	while($row = mysqli_fetch_array($result))
  	{
  		$teamnum=$row["teamnum"];

  		// load team array
		$team[$teamnum]=$row;

		// load sort array
		$teamsrank[$teamnum]=$row[$sort];

	}


  if ($edit)
  {
    // if in edit mode, signal save with edit=2
  	print "<form method=\"POST\" action=\"/rank.php?edit=2&sort={$sort}\">\n";
  	// add hidden field for op
  	hiddenfield( "op", "Save");
  }

  // add edit link or submit button
  //
  //  this part is a little tricky:
  //    instead of using the normal post field of op for the Save button, we set a different name for each
  //    place the button is pressed. Then the results processing logic scans through the post fields and determines
  //    how to set the sort variable.  The normal "Save" is stored as a hidden value that is overwritten by a cancel operation.
  //
  $url_root="/rank.php?edit=${edit}&sort=";					// note the sort is adjustable

  print "\n\n";
  // show edit
  print dblockshowedit($edit, $dblock, $url_root . $sort . "&lsort=" . $lsort) . "\n";
  // Return navigation
  print "\n<br><a href=\"{$base}\">Return to Home</a>\n";


  // set up table heading
  print "
  <!--- table for display data --->
  <table valign=\"top\">
  <tr><th></th>
  "; // end of print


  // display column headings
  //  iterate through always columns, position columns, then customer columns
  //
  // if in edit, show button otherwise show as link (no link sorting if in edit mode)
  //
  // iterate through array and select heading
  foreach($rank_columns_always as $column=>$col_def)
    if (($column == "rank_overall") && ($edit))
      print "<th>Overall Rank<br>\n<input type=\"submit\" name=\"overall_save\" value=\"Save-Edit\">\n";
    else
    // don't show link if in edit, show reverse sort link if this is lsort column
      if ($edit)
        print "<th>{$col_def['heading']}</th>\n";
      elseif ($column == $lsort)
      {
        if ($order=="a") $revsort="d"; else $revsort="a";
        print "<th><a href=\"{$url_root}rank_overall&lsort={$column}&order={$revsort}\"><b>{$col_def['heading']}*</b></a></th>\n";
      }
      else
        print "<th><a href=\"{$url_root}rank_overall&lsort={$column}&order={$col_def['order']}\">{$col_def['heading']}</a></th>\n";

  // positions rank and rating if positoins is turned on
  if ($field_positions)  // if using field positions
  {
	  // loop through positions
	  for($i=1; $i<4; $i++)
	  {
		  // pos
		  print "<th>";
		    // if edit, show button otherwise show as link
			if ($edit)
			  {
				print "Position {$i} Rank<br>\n<input type=\"submit\" name=\"pos{$i}_save\" value=\"Save-Edit\">\n";
	  			print "</th><th>Position {$i} Rating</th>\n";
			  }
			else
			  {
				print "<a href=\"{$url_root}pos{$i}&lsort=rating_pos{$i}\">Position {$i} Rank</a>";
		  		print "</th><th><a href=\"{$url_root}pos{$i}&lsort=rating_pos{$i}\">Position {$i} Rating</a></th>\n";
		  	  }
	  }
  }

  //
  // look for custom columns and add
  //
  foreach($rank_columns_custom as $column=>$col_def)
    // don't show link if in edit, show reverse sort link if this is lsort column
      if ($edit)
        print "<th>{$col_def['heading']}</th>\n";
      elseif ($column == $lsort)
      {
        if ($order=="a") $revsort="d"; else $revsort="a";
        print "<th><a href=\"{$url_root}rank_overall&lsort={$column}&order={$revsort}\"><b>{$col_def['heading']}*</b></a></th>\n";
      }
      else
        print "<th><a href=\"{$url_root}rank_overall&lsort={$column}&order={$col_def['order']}\">{$col_def['heading']}</a></th>\n";

  // end heaing row
  print "</th></tr>\n";

  //
  // print data fields
  //
  // loop through each entry
  foreach ($teamsrank as $teamnum=>$rank)
  {
    // set edit field values
    $editfield = "<input type=\"text\" name=\"{$teamnum}_{$sort}\" size=4 maxlength=4 value=\"{$team[$teamnum]["{$sort}"]}\">";

  	// display values
  	print "<tr>\n";

  	// print team num, name
    print "<td>" . teamhref($teamnum) . "{$teamnum} - {$team[$teamnum]["name"]}";
    // if nickname, print too
    if ($team[$teamnum]["nickname"]) print " ({$team[$teamnum]["nickname"]})";
    print "</a></td>\n";


    // iterate through columns array and select heading
    //   if in edit, show appropriate edit box
    foreach($rank_columns_always as $column=>$col_def)
    {
      print "<td align=\"center\">";
      if (($column == "rank_overall") && ($edit) && ($sort=="rank_overall"))
        print $editfield;
      else
        print "{$team[$teamnum][$column]}";
      print "</td>\n";
    }

    // positions rank and rating
    if ($field_positions === TRUE)  // if using field positions
    {
		// loop through positions
		for($i=1; $i<4; $i++)
		{
			// rank
			print "\n<td align=\"center\">";
			if (($edit) && ($sort=="pos" . $i)) print $editfield; else print $team[$teamnum]["rank_pos" . $i];
			print "</td><td align=\"center\">{$team[$teamnum]["rating_pos" . $i]}</td>";
		}
	}


   //
   // look for custom columns and add
   //
   foreach($rank_columns_custom as $column=>$col_def)
   {
     // if format isn't null and value isn't null then format
     if (($col_def["format"] != NULL) && ($team[$teamnum][$column] != NULL))
       $value = sprintf($col_def["format"], $team[$teamnum][$column]);
     else
       $value = $team[$teamnum][$column];
     print "<td align=\"center\">{$value}</td>\n";
   }

    // close row
  	print "</tr>\n";
  }

  // close table
  print "</table>";


  $options["tr"] = 0;  // add tr tags


  // show edit at bottom
  print "<br>\n";
  print dblockshowedit($edit, $dblock, $url_root . $sort . "&lsort=" . $lsort) . "\n";


  // close the form if in edit mode
  if ($edit) print "\n</form>\n";




?>

<?php
   pfooter();
  ?>
