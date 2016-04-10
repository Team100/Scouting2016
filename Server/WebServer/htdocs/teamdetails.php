<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - Team Details page
  //
  require "page.inc";

  // get variables
  $teamnum=$_GET["teamnum"];
  $edit=$_GET["edit"];

  // header and setup
  pheader($teamnum . " - Team Details", "titleonly");
  $connection = dbsetup();

  // define lock array, fields arrays
  $dblock = array(table=>"team",where=>"teamnum = {$teamnum}");

  $table_team = array("name","nickname","org","location","students","website","sponsors","rookie_year","history","notes");
  $edit_fields = array("name","nickname","org","location","students","website","sponsors","rookie_year","notes");

  // handle update if returning from edit mode
  if ($edit == 2)
  {
  	// load operation
  	if ( $_POST[op] == "Save" )
	{
	  	// check row
		dblock($dblock,"check");

		// load form fields
		$formfields = fields_load("post", $edit_fields);
		$query = "update team set " . fields_insert("update",$formfields) . " where teamnum = {$teamnum}";
		if (debug()) print "<br>DEBUG-teamdetails: " . $query . "<br>\n";

		// process query
		if (! (@mysqli_query ($connection, $query) ))
			dbshowerror($connection, "die");

		// commit
		if (! (@mysqli_commit($connection) ))
			dbshowerror($connection, "die");
	}

	// abandon lock
	dblock($dblock,"abandon");

    // update completed
    $edit = 0;
   }

   // define lock phrase array
   // lock tables if in edit mode
   if ($edit) dblock($dblock, "lock");  // lock row with current user id


  //
  // get data
  //

  // check teamnum
  if (! ($teamnum)) print "<h1>No Team Number Specified</h1>\n";

  // get team details define result set
  $query="select ". fields_insert("nameonly",NULL,$table_team) . " from team where teamnum = {$teamnum}";
  if (debug()) print "<br>DEBUG-teamdetails: " . $query . "<br>\n";
  if (!($result = @ mysqli_query ($connection,$query)))
    dbshowerror($connection);

  // get row
  $row = mysqli_fetch_array($result);

  // print team number, name and nickname as header
  print "<H2>{$teamnum} - {$row["name"]}";
  if ($row["nickname"]) print "({$row["nickname"]})";
  print "</H2>\n";


  //
  // create page
  //

  if ($edit)
  {
    // if in edit mode, signal save with edit=2
  	print "<form method=\"POST\" action=\"/teamdetails.php?edit=2&teamnum={$teamnum}\">\n"
    . hiddenfield( "event_id", $sys_event_id);
  }

  // add edit link or submit button
  print dblockshowedit($edit, $dblock, "/teamdetails.php?teamnum={$teamnum}") . "\n";

  // return and home buttons
  print "&nbsp;&nbsp;&nbsp;<a href=\"/teaminfo.php?teamnum={$teamnum}\">Return to Team Info</a>\n";
  print "&nbsp;&nbsp;&nbsp;<a href=\"/teaminfocompile.php?teamnum={$teamnum}\">Compile Match Evals</a>\n";
  print "&nbsp;&nbsp;&nbsp;<a href=\"/allteamslist.php\">All Teams</a>\n";
  print "&nbsp;&nbsp;&nbsp;<a href=\"{$base}\">Return to Home</a>\n";

  // top of table
  print "
  <!----- Top of entire data page w/ image ----->
  <table valign=\"top\">
  <tr valign=\"top\">
  <td>

  <!---- top of two column Data table ---->
  <table valign=\"top\">
  <tr valign=\"top\">
  <td>
   ";

  // field options
  $options["tr"] = TRUE;  // add tr tags

  print "
  <!--- table for display data --->
  <table valign=\"top\">"
  . tabtextfield($edit,$options,$row, "name","Team name:",15,30)
  . tabtextfield($edit,$options,$row, "nickname","Team nickname (from us):",15,30)
  . tabtextfield($edit,$options,$row, "org","Team Organization",20,60)
  . tabtextfield($edit,$options,$row, "location","Location: ",20,40);
  print "</table>
  </td>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
  <td>
  <table valign=\"top\">"
  . tabtextfield($edit,$options,$row, "rookie_year","Rookie Year: ",5,5)
  . tabtextfield($edit,$options,$row, "students","Students (#): ",2);

  // set up URL
  $options["href"]=TRUE;
  print tabtextfield($edit,$options,$row, "website","Website: ",20,60);
  // unset URL
  $options["href"]=FALSE;

  print "</table></td></tr></table>\n";

  // table for larger data elements
  print "
  <!--- table for display data --->
  <table valign=\"top\">"
  . tabtextfield($edit,$options,$row, "sponsors","Sponsors: ",40,300);


  // display history
  print "\n<tr valign=\"top\"><td>History: </td>\n";


  //print "\n<tr valign=\"top\"><td>History: </td><td>{$row["history"]}</td></tr>\n";
	//  . tabtextfield($edit,$options,$row, "history","History: ",40,300);
    //
    // show table of history events: year, regional name, award
    //   sort by year, event_id, award_type
    //
    print "<td><table border=\"1\">\n<tr><th>Year</th><th>Event</th><th>Awards</th></tr>\n";

    // show history

    // query history rows
    $query = "select year, long_name, award_name
    from team_history a, team_history_award b
    where a.teamnum = {$teamnum}
    and a.teamnum = b.teamnum
    and a.event_id = b.event_id";

    if (debug()) print "<br>DEBUG-teaminfo: " . $query . "<br>\n";
    if (!($result = @mysqli_query ($connection, $query)))
      dbshowerror($connection);

    while ($row = mysqli_fetch_array($result))
    {
      print "<tr><td>{$row['year']}</td><td>{$row['long_name']}</td><td>{$row['award_name']}</td></tr>\n";
    }

    // end history table
    print "</table></td></tr>\n";

  print "</table>
  <!--- end of cell in main table --->
  </td>
  ";


  // end var section and show image
  if ( file_exists ("teamimages/team-{$teamnum}-med.jpg"))
    print "<td>
    <img src=\"/teamimages/team-{$teamnum}-med.jpg\" alt=\"Team ${teamnum} thumb\" title=\"Team {$teamnum}\" width=\"160\" height=\"160\"/>
    </td>";

  // close row/table
  print "</tr>\n</table>\n";

  // notes field / box
  print tabtextarea($edit,$options,$row, "notes","Notes on Team:",6,100);

  // close the form if in edit mode
  if ($edit) print "\n</form>\n";


  //
  // images
  //

  for( $x=1; $x<=6; $x++)
  {
    if (file_exists ("teamimages/team-{$teamnum}-{$x}.jpg"))
      print "<p>
      <img src=\"/teamimages/team-{$teamnum}-{$x}.jpg\" alt=\"Team ${teamnum} image {$x}\" title=\"Team {$teamnum}\" width=\"1200\" />
      ";
  }

?>

<?php
   pfooter();
  ?>
