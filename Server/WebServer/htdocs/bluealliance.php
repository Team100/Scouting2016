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
  pheader("Blue Alliance Updates");
  $connection = dbsetup();

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

      // get data
      try
      {
        $tba_url = "http://www.thebluealliance.com/api/v2/event/{$sys_event_id}/teams";
        $tba_response = \Httpful\Request::get($tba_url)
           ->addHeader('X-TBA-App-Id',$tbaAppId)
           ->send();
      } catch (Exception $e)
      {
      print "Exception";
         showerror("Caught exception from Blue Alliance: " . $e->getMessage());
         return;
      }

      foreach($tba_response->body as $key=>$teamobj)
      {
        // get teamnum
        $tba_dbarray = tba_map_teamnum($teamobj, "");

        // map fields from response for team table
        $tba_dbarray = tba_mapfields($tba_team_to_team, $teamobj, $tba_dbarray);

        // update event data in event table
        tba_updatedb("team", array ("teamnum"=>$tba_dbarray["teamnum"]), $tba_dbarray);

        // get teamnum and reset db array
        $tba_dbarray = tba_map_teamnum($teamobj, "");
        $tba_dbarray['event_id']=$sys_event_id;

        // map fields from response for teambot  table
        $tba_dbarray = tba_mapfields($tba_team_to_teambot, $teamobj, $tba_dbarray);

        // update event data in event table
        tba_updatedb("teambot", array ("event_id"=>$sys_event_id, "teamnum"=>$tba_dbarray["teamnum"]), $tba_dbarray);

      }

      // commit
      if (! (@mysqli_commit($connection) ))
        dbshowerror($connection, "die");

      // Inform user
      print "... Blue Alliance loading complete.<br><br>\n";

      break;

    // ****
    //
    case "matchdata":
    case "eventteams":

      // inform user
      print "Processing event teams...<br>\n";

      // get data
      try
      {
        $tba_url = "http://www.thebluealliance.com/api/v2/event/{$sys_event_id}/teams";
        $tba_response = \Httpful\Request::get($tba_url)
           ->addHeader('X-TBA-App-Id',$tbaAppId)
           ->send();
      } catch (Exception $e)
      {
      print "Exception";
         showerror("Caught exception from Blue Alliance: " . $e->getMessage());
         return;
      }

      foreach($tba_response->body as $key=>$teamobj)
      {
        // get teamnum
        $tba_dbarray = tba_map_teamnum($teamobj, "");

        // map fields from response for team table
        $tba_dbarray = tba_mapfields($tba_team_to_team, $teamobj, $tba_dbarray);

        // update event data in event table
        tba_updatedb("team", array ("teamnum"=>$tba_dbarray["teamnum"]), $tba_dbarray);

        // get teamnum and reset db array
        $tba_dbarray = tba_map_teamnum($teamobj, "");
        $tba_dbarray['event_id']=$sys_event_id;

        // map fields from response for teambot  table
        $tba_dbarray = tba_mapfields($tba_team_to_teambot, $teamobj, $tba_dbarray);

        // update event data in event table
        tba_updatedb("teambot", array ("event_id"=>$sys_event_id, "teamnum"=>$tba_dbarray["teamnum"]), $tba_dbarray);

      }

      // commit
      if (! (@mysqli_commit($connection) ))
        dbshowerror($connection, "die");

      // Inform user
      print "... Blue Alliance loading complete.<br><br>\n";

      break;

    // ****
    //
    case "stats":

      break;

    // ****
    //
    case "history":

      break;

    // ****
    //
    case "allteams":

      break;



    default:

  }


  //
  // Page formatting
  //

  print "<a href=\"{$base}\">Return to Home</a><br>\n";

  // format inside a table
  // print "<br><table border=\"0\">\n<tr>\n";

  print "
  <h4><u>Update Functions</u></h4>
  <ul>
  <li><a href=\"/bluealliance.php?op=eventteams\">Update team information for current event</a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=matchdata\">Update match data for current event</a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=stats\">Get stats for teams in matches</a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=\"></a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=\"></a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=\"></a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=\"></a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=\"></a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=\"></a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=history\">Update history and award info for teams in our database</a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=allteams\">Update all FIRST teams (lots of data)</a></li>
  <br>

  </ul>
  "; // end of print


  print "<br><br><a href=\"{$base}\">Return to Home</a><br>\n";

  pfooter();
 ?>