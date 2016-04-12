<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - Team Info Heading Page
  // 
  // Shared by team info pages as inline insert
  //
  //
  // Assumes two variables are defined before this code is hit:
  //  $teamnum
  //  $page_event_id (event_id used in this page)
  //  $page_allow_edits -- TRUE show edit buttons, FALSE does not
  //


  // check parameter and update if needed
  if (isset($_GET['needseval'])) set_user_prop("needeval", $_GET['needseval']);
  // check value
  $needseval = test_user_prop("needeval");

  // define lock array, fields arrays
  $dblock = array(table=>"teambot",where=>"event_id = '{$page_event_id}' and teamnum = {$teamnum}");

  // teambot array
  $table_teambot = array_merge ( array("rank_overall","rating_overall","rating_overall_off","rating_overall_def",
		"rank_pos1","rating_pos1","rank_pos2","rating_pos2","rank_pos3","rating_pos3","offense_analysis",
		"defense_analysis","pos1_analysis","pos2_analysis","pos3_analysis","robot_analysis","driver_analysis",
		"with_recommendation","against_recommendation"),
		param_array("Play"));

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

      $query = "update teambot set " . fields_insert("update",$formfields) . " where event_id = '{$page_event_id}' and teamnum = {$teamnum}";
      // process query
      if (debug()) print "<br>DEBUG-teaminfo: " . $query . "<br>\n";
      if (! (@mysqli_query ($connection, $query) ))
          dbshowerror($connection, "die");

      // commit
      if (! (@mysqli_commit($connection) ))
           bshowerror($connection, "die");
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
  $editURL = "{$pagename}?teamnum={$teamnum}";

  //
  // begin display page
  //

  print "\n<!----- Top of page ----->\n\n";

  // check teamnum
  if (! ($teamnum)) showerror("<h1>No Team Number Specified</h1>","die");

  // get basic teaminfo details define result set
  if (!($result = @ mysqli_query ($connection,
  	"select name, nickname from team where teamnum = {$teamnum}")))
    dbshowerror($connection);
  // get row
  $row = mysqli_fetch_array($result);

  // print team number, name and nickname as header
  print "<H2>" . $page_heading . team_needs_eval_str($teamnum) . " - {$row["name"]}";
  if ($row["nickname"]) print " ({$row["nickname"]})";
  print "</H2>\n";

  // form
  // if in edit mode, signal save with edit=2
  if ($edit)
  	print "<form method=\"POST\" action=\"{$pagename}?edit=2&teamnum={$teamnum}\">\n";


  //
  // formatting table
  //

  print "
  <!----- Top of formatting table ----->
  <table valign=\"top\">
  <tr valign=\"top\">
   <td>";

  // Navigation Link to team details
  print "{$nav1_before}{$teamnum}{$nav1_after}\n";
  print "<a href=\"/teammatches.php?teamnum={$teamnum}\">Team Matches Evaluation</a><br>\n";

  // space before details
  print "<br>\n";

  // add game-specific fields and stats columns
  foreach($RankFields as $rankfield)
    if ($rankfield['display'] != NULL ) $rankcolumns = $rankcolumns . $rankfield['column'] . ", ";

  // add stats columns to rankcolumns
  foreach($stats_columns as $statcolumn=>$statarray)
    $rankcolumns = $rankcolumns . $statcolumn . ", ";

  // get team details define result set
  $query = "select ". $rankcolumns . fields_insert("nameonly",NULL,$table_teambot)
   . " from teambot where event_id = '{$page_event_id}' and teamnum = {$teamnum}";

  if (debug()) print "<br>DEBUG-teaminfo: " . $query . "<br>\n";
  if (!($result = @ mysqli_query ($connection,$query)))
    dbshowerror($connection);

  // get row
  $row = mysqli_fetch_array($result);

  // buttons / links

  // add edit link or submit button
  if ($page_allow_edits)
    print dblockshowedit($edit, $dblock, $editURL) . "\n";

  // return and home buttons
  print "<br>";
  // print "<br><br><a href=\"/teaminfo.php?teamnum={$teamnum}\">Return to Team Info</a><br>\n";
  print "<a href=\"{$base}\">Return to Home</a>\n";

  // additional links
  print "</td>\n<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
  print "<td>";
  print "<a href=\"/teamdetails.php?teamnum={$teamnum}\">Team Details &amp; Photos<a><br>\n";
  print "<a href=\"/teamotherevents.php?teamnum={$teamnum}\">Other Season Events<a><br>\n";

  // 3rd column of links
  print "</td>\n<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
  print "<td>";

  // show needs eval feature on/off link
  print "<a href=\"{$pagename}?teamnum={$teamnum}&needseval=";
  if ($needseval == 1) print "0\">Hide"; else print "1\">Show";
  print " Needs Eval</a>\n";
  
  // end links and place picture
  print "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
  // 
  // end

  // show image
  if ( file_exists ("teamimages/team-{$teamnum}-med.jpg"))
    print "<td>
    <img src=\"/teamimages/team-{$teamnum}-med.jpg\" alt=\"Team ${teamnum} thumb\" title=\"Team {$teamnum}\" width=\"80\" height=\"80\"/>
    </td>";

  // close row/table
  print "</tr>\n</table>\n";

  //
  // end of nav and top potion of page
  //