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
      print "Updated team \n";

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

        // inform user
        print "{$tba_dbarray["teamnum"]}, ";

      }

      // commit
      if (! (@mysqli_commit($connection) ))
        dbshowerror($connection, "die");

      // Inform user
      print "<br>&nbsp;&nbsp;&nbsp; ... Blue Alliance loading complete.<br><br>\n";

      break;

    // ****
    // get match data
    case "matchdata":
      // inform user
      print "Processing match data...<br>\n";
      print "Updated match \n";

      // get data
      try
      {
        $tba_url = "http://www.thebluealliance.com/api/v2/event/{$sys_event_id}/matches";
        $tba_response = \Httpful\Request::get($tba_url)
           ->addHeader('X-TBA-App-Id',$tbaAppId)
           ->send();
      } catch (Exception $e)
      {
      print "Exception";
         showerror("Caught exception from Blue Alliance: " . $e->getMessage());
         return;
      }

      foreach($tba_response->body as $key=>$matchobj)
      {
        // get match array from match
        $matcharray = tba_getmatcharray($matchobj);

        // create match ID array and use and starting dbarray
        $tba_dbarray = array_merge ( array ("event_id"=>$sys_event_id, ), $matcharray);
        $match_id_array = array ("event_id"=>$sys_event_id, "type"=>$matcharray['type'],
                            "matchnum"=>$matcharray['matchnum']);

        // map fields from response for match_instance table
        // $tba_dbarray = tba_mapfields($tba_match_to_match_instance, $matchobj, $tba_dbarray);

        // update event data in event table

        tba_updatedb("match_instance", $match_id_array, $tba_dbarray);

        // update match alliance table
        // iterate match alliances
        foreach($matchobj->alliances as $colorkey=>$allianceobj)
        {
          if ($colorkey == "blue") $color='B'; else $color='R';

          // set match alliance array
          $match_alliance_array = array_merge ($match_id_array,array("color"=>$color));
          $tba_dbarray = $match_alliance_array;

          // map fields from response for match_instance table
          $tba_dbarray = tba_mapfields($tba_match_to_match_alliance, $allianceobj, $tba_dbarray);

          // update event data in event table
          tba_updatedb("match_instance_alliance", $match_alliance_array, $tba_dbarray);

          // loop through each team in alliance and update match_team table
          foreach($allianceobj->teams as $teamkey)
          {
            // turn teamkey to teamnum
            sscanf($teamkey, "frc%d", $teamnum);

            // set match key array
            //   Note: it doesn't use color in the kay or the alliance key;
            //     however, color must be inserted in the table (avoids a crazy join)
            $match_team_array = array_merge ($match_id_array, array ("teamnum"=>$teamnum));
            $tba_dbarray = $match_team_array;
            // map fields from response for match_instance table
            // $tba_dbarray = tba_mapfields($tba_match_to_match_team, $match_team_array, $tba_dbarray);

            // manually add color
            $tba_dbarray = array_merge ($tba_dbarray, array("color"=>$color));

print_r($tba_array);
print "<br>\n";

            // update event data in event table
            tba_updatedb("match_team", $match_team_array, $tba_dbarray);
          }
        } // end of alliance

        // inform user
        print "{$tba_dbarray["type"]}{$tba_dbarray["matchnum"]}, ";

      } // end of match

      // commit
      //if (! (@mysqli_commit($connection) ))
      //  dbshowerror($connection, "die");

      // Inform user
      print "<br>&nbsp;&nbsp;&nbsp; ... Blue Alliance loading complete.<br><br>\n";

      break;

    // ****
    //
    case "stats":
      print "<br>Not yet implemented.<br><br>\n";


      break;

    // ****
    // Load team history and team awards in two tables
    case "history":

      print "<br>Not yet implemented.<br><br>\n";


      break;

      // inform user
      print "Processing team histories and awards...<br><br>\n";
      print "Updated team \n";

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

        // inform user
        print "{$tba_dbarray["teamnum"]}, ";

      }

      // commit
      if (! (@mysqli_commit($connection) ))
        dbshowerror($connection, "die");

      // Inform user
      print "<br>&nbsp;&nbsp;&nbsp; ... Blue Alliance loading complete.<br><br>\n";

      break;

    // ****
    //
    case "allteams":
      print "<br>Not yet implemented.<br><br>\n";


      break;



    default:

  }


  //
  // Page formatting
  //

  print "<a href=\"{$base}\">Return to Home</a><br>\n";

  print "
  <h4><u>Update Functions</u></h4>
  <ul>
  <li><a href=\"/bluealliance.php?op=eventteams\">Update team information for current event</a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=matchdata\">Update match data for current event</a></li>
  <br>
  <li><a href=\"/bluealliance.php?op=stats\">Get stats and rankings for teams in matches</a></li>
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