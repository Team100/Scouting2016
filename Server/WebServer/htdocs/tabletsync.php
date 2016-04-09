<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System
  // Tablet Syncr
  //
  // Syncs JSON files and images with tablet server
  //
  // Use $map_team_tags and $map_match_tags arrays in params.inc
  //    to map the non-custom fields being entered from tablets
  //

  require "page.inc";
  require "bluealliance.inc";

  // header and setup
  pheader("Tablet Sync Services");
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
    //
    case "match_eval":
      // inform user of pending operation
      print "Processing match data from tablets...<br>\n";

      // map array of tags to field number for custom mapping
      for ($i=0; null !== $dispfields["Match"][$i];  $i++)
        $tagmap[$dispfields["Match"][$i]["tag"]] = $i;

      // scan upload directory for files
      if (! ($dir = scandir($tablet_ingest_match)))
        print "<br><br><b>!!! System error: ingest directory '{$tablet_ingest_match}' not found for scanning files.<br><br>\n";
      else
      {
        foreach($dir as $file)
          // check if json file
          if (preg_match('/.*\.json$/',$file))

          {
            // read file
            $json_file = file_get_contents($tablet_ingest_match . "/" . $file);

            // fix anything that we need to in the file
            $json_file = str_replace("NaN", "0", $json_file);

            // decode json and process
			if (! ($json_array = json_decode($json_file)))
			{
			  print "<br>!! JSON conversion failed for $file<br>\n";

			  // move file to unprocessed/error recovery
			  rename($tablet_ingest . "/" . $file, $tablet_ingest_error . "/" . $file);
			  print "Moved {$file} to unprocessed error directory.<br>\n";
			}
			else
			{
			  // build update array using $map_match_tags

			  // set up default match identifier
			  // temporary (JLV) - assume type = Q
			  $match_identifiers = array("event_id"=>$sys_event_id, "type"=>"Q");

			  // initialize $db_array
			  $db_array = [];

			  foreach($json_array as $jsontag=>$jsonvalue)
			  {
			    // if a key tag, built tab_identifiers
			    if ($jsontag == "teamnum")
			      $match_identifiers = array_merge($match_identifiers, array ("teamnum"=>$jsonvalue));
			    // check match number
				elseif ($jsontag == "matchnum")
			      $match_identifiers = array_merge($match_identifiers, array ("matchnum"=>$jsonvalue));
			    // check default / system-defined parameters
			    elseif ((isset($map_match_tags[$jsontag])) && ($jsonvalue != NULL))
			      $db_array = array_merge($db_array, array($map_match_tags[$jsontag]=>$jsonvalue));
			    // check custom parameters
			    elseif ((isset($tagmap[$jsontag])) && ($jsontag != NULL))
			    {
			      // truncate string to match db
			      $truncstring = substr($jsonvalue, 0, $tablet_max_matchfield);

			      $db_array = array_merge($db_array, array("MatchField_" . $tagmap[$jsontag]=>$truncstring));
			    }
			    // if not found, no action needed
			  } // end of foreach

			  // update database
			  db_update("match_team", $match_identifiers, $db_array);

			  // commit
			  if (! (@mysqli_commit($connection) ))
                dbshowerror($connection, "die");

              // move file to processed
              rename($tablet_ingest_match . "/" . $file, $tablet_ingest_complete . "/" . $file);

			  // inform user
			  print "Completed {$file}.<br>\n";

			} // end of json decode
		  }
        }
      // inform of completed function
      print "<br>Match evaluations loaded.<br><br>\n";

      break;

    // ****
    //
    case "pit_eval":

      // inform user of pending operation
      print "Processing pit scouting data from tablets...<br>\n";

      // map array of tags to field number for custom mapping
      for ($i=0; null !== $dispfields["Play"][$i];  $i++)
        $tagmap[$dispfields["Play"][$i]["tag"]] = $i;

      // scan upload directory for files
      if (! ($dir = scandir($tablet_ingest_pit)))
        print "<br><br><b>!!! System error: pit ingest directory '{$tablet_ingest_pit}' not found for scanning files.<br><br>\n";
      else
      {
        foreach($dir as $file)
          // check if json file
          if (preg_match('/.*\.json$/',$file))

          {
            // read file
            $json_file = file_get_contents($tablet_ingest_pit . "/" . $file);

            //
            // fix anything that we need to in the file
            //

            // Nan error
            $json_file = str_replace("NaN", "0", $json_file);

            // replace true/false to numeric rating
            $json_file = str_replace("false", "0", $json_file);
            $json_file = str_replace("true", "1", $json_file);

            // remove nesting
            $json_file = str_replace('"defenses":{', "", $json_file);
            $json_file = str_replace('},', ",", $json_file);

            // decode json and process
			if (! ($json_array = json_decode($json_file)))
			{
			  print "<br>!! JSON conversion failed for $file<br>\n";

			  // move file to unprocessed/error recovery
			  rename($tablet_ingest . "/" . $file, $tablet_ingest_error . "/" . $file);
			  print "Moved {$file} to unprocessed error directory.<br>\n";
			}
			else
			{

			  // build update array using $map_match_tags

			  // set up default match identifier
			  // temporary (JLV) - assume type = Q
			  $teambot_Identifiers = array("event_id"=>$sys_event_id);

			  // initialize $db_array
			  $db_array = [];

			  foreach($json_array as $jsontag=>$jsonvalue)
			  {
			    // if a key tag, built tab_identifiers
			    if ($jsontag == "teamnum")
			      $teambot_Identifiers = array_merge($teambot_Identifiers, array ("teamnum"=>$jsonvalue));
			    // check custom parameters
			    elseif ((isset($tagmap[$jsontag])) && ($jsontag != NULL))
			    {
			      // truncate string to match db
			      $truncstring = substr($jsonvalue, 0, $tablet_max_matchfield);

			      $db_array = array_merge($db_array, array("PlayField_" . $tagmap[$jsontag]=>$truncstring));
			    }
			    // if not found, no action needed
			  } // end of foreach

			  // update database field at a time, only if field is null
			  db_update_or_create_if_new("teambot", $teambot_Identifiers, $db_array);

			  // commit
			  if (! (@mysqli_commit($connection) ))
                dbshowerror($connection, "die");

              // move file to processed
              rename($tablet_ingest_pit . "/" . $file, $tablet_ingest_complete . "/" . $file);

			  // inform user
			  print "Completed {$file}.<br>\n";

			} // end of json decode
		  }
        }
      // inform of completed function
      print "<br>Match evaluations loaded.<br><br>\n";

      break;

    // ****
    //
    case "match_template":
      break;

    // ****
    //
    case "pit_template":

      $query = "select teambot.teamnum, name, nickname, location, org, students, rookie_year
           from teambot, team where teambot.teamnum = team.teamnum";

       if (!($result = @mysqli_query ($connection, $query)))
            dbshowerror($connection);
       while ($row = mysqli_fetch_array($result))
       {
          $fp = fopen($tablet_templates . '/' . $row['teamnum'] . '.json', 'w');
          fwrite($fp, json_encode($row));
          fclose($fp);
       }

      // Inform user
      print "<br>Table pit scouting templates created<br><br>\n";

      break;

    // ****
    //
    case "export":

      $query = "select event_id, teamnum,
  offense_analysis,
  defense_analysis,
  robot_analysis,
  driver_analysis,
  with_recommendation,
  against_recommendation,
  notes
  from teambot
  where
  event_id = '{$sys_event_id}' and
  offense_analysis is not NULL or
  defense_analysis is not  NULL or
  robot_analysis is not NULL or
  driver_analysis is not NULL or
  with_recommendation is not NULL or
  against_recommendation is not NULL or
  notes is not NULL
  ";

       if (debug()) print "<br>DEBUG:tabletsync, Select query: " . $query . "<br>\n";
       if (!($result = @mysqli_query ($connection, $query)))
            dbshowerror($connection);
       while ($row = mysqli_fetch_array($result))
       {
          if (debug())
          {
            print "<br>DEBUG: row result ";
            print_r($row);
            print "<br>\n";
          }
          $fp = fopen($tablet_export . '/export.json', 'a');
          fwrite($fp, json_encode($row));
          fwrite($fp, "\n");
          fclose($fp);
       }

      // Inform user
      print "<br>JSON export of analysis fields completed<br><br>\n";

      break;

    // ****
    //
    case "photo":
      break;


    default:

  }


  //
  // Page formatting
  //

  print "<a href=\"{$base}\">Return to Home</a><br>\n";

  print "
  <h4><u>Tablet Sync Functions</u></h4>
  <ul>
  <li><a href=\"/tabletsync.php?op=match_eval\">Upload match evaluations</a></li>
  <br>
  <li><a href=\"/tabletsync.php?op=pit_eval\">Upload pit evaluations</a></li>
  <br>
  <li><a href=\"/tabletsync.php?op=match_template\">Prepare match templates</a></li>
  <br>
  <li><a href=\"/tabletsync.php?op=pit_template\">Prepare pit templates</a></li>
  <br>
  <li><a href=\"/tabletsync.php?op=photo\">Upload and test photos</a></li>
  <br>
  <li><a href=\"/tabletsync.php?op=export\">Export non-null analysis fields</a></li>
  <br>
  </ul>
  "; // end of print


  print "<br><br><a href=\"{$base}\">Return to Home</a><br>\n";

  pfooter();
 ?>