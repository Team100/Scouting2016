<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - finals selection from field
  //

  require "page.inc";
  // get variables

  pheader("Template", "titleonly", array ("openhead"=>1) );
  $connection = dbsetup();

  // add retrieve header
  print "<meta http-equiv=\"refresh\" content=\"{$message_refresh}\">\n";

  // close head and body
  print "</HEAD>\n<BODY>\n";


  // show top 2 ranked teams
  //

  		$query = "select teamnum from teambot where event_id = '${sys_event_id}' and
  		         teamnum not in (select teamnum from alliance_unavailable) order by rank_overall";
      if (!($result = @ mysqli_query ($connection, $query)))
  	    dbshowerror($connection);
    	if (! ($row = mysqli_fetch_array($result)))
    		showerror("Match info not found.  Please try again.","die");
    	$teamnum = $row["teamnum"];
    	print "<br><p style=\"font-size:80px;\">Team: $teamnum</p><br>";




  // get message from stands
  if (! ($result = @ mysqli_query ($connection, "select message from message where facility = 'finals_selection'" ) ))
		dbshowerror($connection, "die");

  $message = mysqli_fetch_array($result);


   print "<p style=\"font-size:80px;\">{$message["message"]}</p>";

  ?>

<?php
   pfooter();
 ?>