<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - Team Info / Robot page
  //   (should be named teambotinfo)
  //
  require "page.inc";

  // get variables
  $teamnum=$_GET["teamnum"];
  $edit=$_GET["edit"];

  // header and setup
  pheader("Team Robot Info - " . $teamnum, "titleonly");
  $connection = dbsetup();

  // define lock array, fields arrays
  $dblock = array(table=>"teambot",where=>"event_id = '{$sys_event_id}' and teamnum = {$teamnum}");

  // teambot array
	$table_teambot = array_merge ( array("rank_overall","rating_overall","rating_overall_off","rating_overall_def",
		"rank_pos1","rating_pos1","rank_pos2","rating_pos2","rank_pos3","rating_pos3","offense_analysis",
		"defense_analysis","pos1_analysis","pos2_analysis","pos3_analysis","robot_analysis","driver_analysis",
		"with_recommendation","against_recommendation"),
		param_array("Play"), param_array("Pit"));

  // handle update if returning from edit mode
	if ($edit == 2)
	{
		// load operation
		if ( $_POST[op] == "Save" )
		{
			// check db
			dblock($dblock,"changedby");
			dblock($dblock,"check");

			// load form fields
			$formfields = fields_load("post", $table_teambot);

			$query = "update teambot set " . fields_insert("update",$formfields) . " where event_id = '{$sys_event_id}' and teamnum = {$teamnum}";
			// process query
			if (! (@mysqli_query ($connection, $query) ))
				dbshowerror($connection, "die");
			// commit
			if (! (@mysqli_commit($connection) ))
				dbshowerror($connection, "die");
		}


		// abandon row
		dblock($dblock,"abandon");

		// update completed
		$edit = 0;
	}

	// define lock phrase array
	// lock tables if in edit mode
	if ($edit) dblock($dblock,"lock");  // lock row with current user id
	// define edit URL
	$editURL = "/teaminfo.php?teamnum={$teamnum}";

?>

<!----- Top of page ----->


<?php
  //
  // create page
  //

  // check teamnum
  if (! ($teamnum)) showerror("<h1>No Team Number Specified</h1>","die");

  // get basic teaminfo details define result set
  if (!($result = @ mysqli_query ($connection,
  	"select name, nickname from team where teamnum = {$teamnum}")))
    dbshowerror($connection);
  // get row
  $row = mysqli_fetch_array($result);

  // print team number, name and nickname as header
  print "<H2>Team Robot Info - {$teamnum} - {$row["name"]}";
  if ($row["nickname"]) print " ({$row["nickname"]})";
  print "</H2>\n";

  // form
  // if in edit mode, signal save with edit=2
  if ($edit)
  	print "<form method=\"POST\" action=\"/teaminfo.php?edit=2&teamnum={$teamnum}\">\n";


  print "
  <!----- Top of formatting table ----->
  <table valign=\"top\">
  <tr valign=\"top\">
   <td>";

  // Navigation Link to team details
  print "<a href=\"/teamdetails.php?teamnum={$teamnum}\">Team Details &amp; Photos<a><br>\n";
  print "<a href=\"/teammatches.php?teamnum={$teamnum}\">Team Matches Evaluation<a><br>\n";

  // space before details
  print "<br>\n";

  // get team details define result set
  if (!($result = @ mysqli_query ($connection,
  	"select ". fields_insert("nameonly",NULL,$table_teambot) . " from teambot where event_id = '{$sys_event_id}' and teamnum = {$teamnum}")))
    dbshowerror($connection);

  // get row
  $row = mysqli_fetch_array($result);

  // buttons / links

  // add edit link or submit button
  print dblockshowedit($edit, $dblock, $editURL) . "\n";

  // return and home buttons
  print "<br>";
  // print "<br><br><a href=\"/teaminfo.php?teamnum={$teamnum}\">Return to Team Info</a><br>\n";
  print "<a href=\"{$base}\">Return to Home</a>\n";

  // end links and place picture
  print "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";

  // show image
  if ( file_exists ("teamimages/team-{$teamnum}-med.jpg"))
    print "<td>
    <img src=\"/teamimages/team-{$teamnum}-med.jpg\" alt=\"Team ${teamnum} thumb\" title=\"Team {$teamnum}\" width=\"80\" height=\"80\"/>
    </td>";

  // close row/table
  print "</tr>\n</table>\n";

  //
  // main info portion of page
  //
  require "teaminfofields.inc";

  // close the form if in edit mode
  if ($edit) print "\n</form>\n";

	showupdatedby($dblock);

	pfooter();

?>