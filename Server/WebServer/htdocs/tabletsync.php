<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System
  // Tablet Syncr
  //
  // Syncs JSON files and images with tablet server
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
      break;

    // ****
    //
    case "pit_eval":
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
  <li><a href=\"/tabletsync.php?op=match_eval\">Upload match evaluation</a></li>
  <br>
  <li><a href=\"/tabletsync.php?op=pit_eval\">Upload pit evaluation</a></li>
  <br>
  <li><a href=\"/tabletsync.php?op=match_template\">Prepare match templates</a></li>
  <br>
  <li><a href=\"/tabletsync.php?op=pit_template\">Preapre pit templates</a></li>
  <br>
  <li><a href=\"/tabletsync.php?op=photo\">Upload and test photos</a></li>
  <br>
  </ul>
  "; // end of print


  print "<br><br><a href=\"{$base}\">Return to Home</a><br>\n";

  pfooter();
 ?>