#!/usr/bin/php
<?php
  // 
  // $Revision: 3.0 $
  // $Date: 2016/03/14 23:00:02 $
  //
  // Parses team variables from an HTML file and adds to database

  //
  // Database parameters
  //

$dbname = "competition";

$dbuser = "compuser";

$dbpass = "3006redrock";

$dbhost = "localhost";



  // args
if ($argc > 1)
  $filename = $argv[1];
else
  {
    print "Usage: team-extact html-file-name\n";
    exit;
  }

// get file into string
if ( file_exists ($filename))
  {
    $file = file_get_contents ( $filename );

    // find number
    //    if ( ereg( 'Team Number</td>.*<td><b>(.*)</b></td>', $file, $ret))
    if ( ereg( 'Team Number</td>[[:space:]]*<td><b>([^<]*)</b></td>', $file, $ret))
      $fields["teamnum"] = $ret[1];

    // team name / sponsors
    if ( ereg( 'Team Name</td>[[:space:]]*<td>([^<]*)</td>', $file, $ret))
      {
	$fields["sponsors"] = $ret[1];

	// org
	$org = substr(strrchr($ret[1], '&'), 2);
	if ($org === FALSE) $org = $ret[1];
	$fields["org"] = $org;

      }
    
    // location
    if ( ereg( 'Team Location</td>[[:space:]]*<td>([^<]*)</td>', $file, $ret))
      $fields["location"] = $ret[1];


    // rookie_year
    if ( ereg( 'Rookie Season</td>[[:space:]]*<td>([^<]*)</td>', $file, $ret))
      $fields["rookie_year"] = $ret[1];


    // team name
    if ( ereg( 'Team Nickname</td>[[:space:]]*<td>([^<]*)</td>', $file, $ret))
      $fields["name"] = $ret[1];


    // web site
    if ( ereg( 'Team Website</td>[[:space:]]*<td><a href=[^<]*>([^<]*)</a></td>', $file, $ret))
      {
	// strip off http:if there
	if ( ! ($web = (strstr($ret[1], 'http://'))) === FALSE)
	  $fields["website"] = substr($web, 7);
	else
	  $fields["website"] = $ret[1];

	// cut trailing /
	if ( substr($fields["website"],strlen($fields["website"])-1,1) == "/")
	  $fields["website"] = 
	    substr($fields["website"],0,strlen($fields["website"])-1);

      }

    // history
    if ( ereg( 'Team History</td>[[:space:]]*<td>(.*)</td>', $file, $ret))
      $fields["history"] = substr($ret[1], 0, strpos($ret[1],'</table>')+8);
      //      $fields["history"] = substr($ret[1], 0, strpos($ret[1],'</table>'));
    //$fields["history"] = $ret[1];

    //
    // database work

    if(!($connection = @ mysqli_connect($dbhost,$dbuser,$dbpass, $dbname)))
      die("Database Error:" 
	  . mysqli_connect_errno() . " : " . mysqli_connect_error());
     
    // turn autocommit off
    mysqli_autocommit($connection, FALSE);

    $insert = "insert into team (";
    
    // column names
    $cols = "";
    foreach ($fields as $key => $value)
      {
	// add comma
	if ( $cols ) $cols = $cols . ", ";

	$cols = $cols . $key;
      }

    $insert = $insert . $cols . ") values ( ";

    // values
    $vals = "";
    foreach ($fields as $key => $value)
      {
	// add comma
	if ( $vals ) $vals = $vals . ", ";

	$vals = $vals . "'" . 
	  mysqli_real_escape_string($connection, $value) . "'";
      }

    $insert = $insert . $vals . ");";


    // debug info
    // var_dump ($fields);
    // print $insert;



    print "Processing team $fields[teamnum]...\n";

    // process query
    if (! (@mysqli_query ($connection, $insert) ))
      die(
	  "Fatal Database Error " . mysqli_errno($connection) . ", " 
	  . mysqli_error($connection) . "\n");

    // commit
    if (! (@mysqli_commit($connection) ))
      die(
	  "Fatal Database Error " . mysqli_errno($connection) . ", " 
	  . mysqli_error($connection) . "\n");

  }

?>
