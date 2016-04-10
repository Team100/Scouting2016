<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - finals selection from field
  //

  require "page.inc";
  // get variables

  pheader("Finals on Field - " . $host_team_name, "titleonly", array ("openhead"=>1) );
  $connection = dbsetup();

  // add retrieve header
  print "<meta http-equiv=\"refresh\" content=\"{$message_refresh}\">\n";

  // close head and body
  print "</HEAD>\n<BODY>\n";


  // show top 2 ranked teams
  //

  	$query = "select teamnum, rank_overall from teambot where event_id = '${sys_event_id}' and
  		         teamnum not in (select teamnum from alliance_unavailable)
  		         order by isnull(rank_overall), rank_overall";
      if (!($result = @ mysqli_query ($connection, $query)))
  	    dbshowerror($connection);

  	  // first ranked team
    	if (! ($row = mysqli_fetch_array($result)))
    		showerror("Match info not found.  Please try again.","die");
    	print "<p style=\"font-size:60px;\">1st Team: {$row['teamnum']} ({$row['rank_overall']})</p>";


  	  // second ranked team
    	if (! ($row = mysqli_fetch_array($result)))
    		showerror("Match info not found.  Please try again.","die");
    	print "<p style=\"font-size:40px;\">2nd Team: {$row['teamnum']} ({$row['rank_overall']})</p>";



  // get message from stands
  if (! ($result = @ mysqli_query ($connection, "select message from message where facility = 'finals_selection'" ) ))
		dbshowerror($connection, "die");

  $message = mysqli_fetch_array($result);


   print "<p style=\"font-size:50px;\">{$message["message"]}</p>";


   pfooter();
 ?>