#!/usr/bin/php
<?php
  // 
  // $Revision: 3.0 $
  // $Date: 2016/03/14 23:00:02 $
  //
  // Parses championship teams an HTML file and creates database insert statements to add to database
  //
  // usage: cmd filename


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
    $file = file ( $filename );

    // cnt is used to mark after team is found
    $cnt=0;
    
    // loop through each line in file
    foreach($file as $line)
    {
      // find number based on team and regexp from team
      if ( ereg( '>([0-9]+)</a></td>', $line, $ret))
	{
	  $teamnum = $ret[1];

	  // indicate found
	  $cnt=1;
	}

      // if we just found a team, check for league
      if ($cnt)
	if ($cnt == 1)
	  // increment
	  $cnt = ++$cnt;
	else
	  {
	    if ( ereg( '<td>([[:alpha:]]+)</td>', $line, $ret))
	      print "insert into championteam (league_name, teamnum) values ('{$ret[1]}', {$teamnum});\r\n";
	  else
	    print "insert into championteam (teamnum) values ({$teamnum});\r\n";

	  // reset found counter
	    $cnt=0;
	  }

    }

    // print statement to set league from long names
    print "\r\n\r\n";
    print "update championteam set championteam.league = 
            (select league from league where league.league_name = championteam.league_name);\r\n";
    
  } // end of file exists if

?>
