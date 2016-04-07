<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System
  // Blue Alliance Data Loader
  //
  // Download and update various forms of Blue Alliance data
  //

  require "page.inc";
  require "bluealliance.inc";

  // header and setup
  pheader("Blue Alliance Update");
  $connection = dbsetup();

  // if not administrator, display error.  Otherwise show admin section.
  if (! $admin)
    print "<h3>You must be an administrator to use this page.</h3>\n";
  else
  {

  // get variables if they exist
  if (isset($_GET["op"])) $op = $_GET["op"]; else $op = "";
  $edit=$_GET["edit"];

  // define lock array, fields arrays

  // handle update if returning from edit mode

  // lock tables if in edit mode
  // if ($edit) dblock($dblock,"lock");  // lock row with current user id

  // process lock

  //   	if ( $_POST[op] == "Save" )


  // branch on operation:

  switch ($op)
  {
    // ****
    // get teams from event
    case "eventteams":

      // inform user
      print "Processing event teams...<br>\n";
      if (tba_get_event_teams())
        print "Blue Alliance operation successful.<br>\n";
      else
        print "Blue Alliance operation failed.  Please check errors.<br>\n";
      print "<br>";
      break;

    // ****
    // get match data
    case "matchdata":

      // inform user
      print "Processing event matches...<br>\n";
      if (tba_get_matches())
        print "Blue Alliance operation successful.<br>\n";
      else
        print "Blue Alliance operation failed.  Please check errors.<br>\n";
      print "<br>";
      break;


    // ****
    // stats and rankings
    case "stats":

      // stats first

      // inform user
      print "Retrieving event stats...<br>\n";
      if (tba_get_event_stats())
        print "Blue Alliance operation successful.<br>\n";
      else
        print "Blue Alliance operation failed.  Please check errors.<br>\n";
      print "<br>\n";

      // rankings
      // inform user
      print "Retrieving rankings...<br>\n";
      if (tba_get_event_rankings())
        print "Blue Alliance operation successful.<br>\n";
      else
        print "Blue Alliance operation failed.  Please check errors.<br>\n";
      print "<br>";

      break;

    // ****
    // Load team history and team awards in two tables
    case "history":

      print "<br>Not yet implemented.<br><br>\n";


      break;

    // ****
    //
    case "allteams":
      print "<br>Not yet implemented.<br>\n";


      print "<br>";
      break;

    // ****
    //
    case "autoupdate";
      $toggle = $_GET['toggle'];

      // set auto_update
      if ($toggle == "on") $state=1; else $state=0;

      if (! (tba_set_autoupdate($state)))
        showerror("Cannot set autoupdate in {$auto_update_file}.");

    default:

  }

  // check on auto-update state
  $state = tba_get_autoupdate();
  if ($state == 1) $auto_state = "off"; else $auto_state = "on";


  //
  // Page formatting
  //

  print "<a href=\"{$base}\">Return to Home</a>\n";
  print "&nbsp;&nbsp;&nbsp; <a href=\"/admin.php\">Sys Admin</a><br>\n";

  print "
  <h4><u>Update Functions and Control</u></h4>
  <ul>
  <li><a href=\"/bluealliance.php?op=eventteams\">Update team information for current event</a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=matchdata\">Update match data for current event</a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=stats\">Get stats and rankings for teams in matches</a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=history\">Update history and award info for teams in our database</a></li>
  <br>
  <br>
  <li><a href=\"/bluealliance.php?op=autoupdate&toggle={$auto_state}\">Turn <b>{$auto_state}</b> automatic updates until {$auto_update_stop}.</a></li>
  <br>
  <br>
  <li><a href=\"/bluealliance.php?op=allteams\">Update all FIRST teams (! be careful - lots of data)</a></li>
  <br>

  </ul>
  "; // end of print

  } // end of "if admin" qualification

  print "<br><br><a href=\"{$base}\">Return to Home</a>\n";
  if ($admin) print "&nbsp;&nbsp;&nbsp; <a href=\"/admin.php\">Sys Admin</a>\n";
  print "<br>\n";

  pfooter();
 ?>