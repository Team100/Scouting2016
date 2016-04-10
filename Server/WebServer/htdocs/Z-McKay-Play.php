<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System
  //
  // Sets event code and name in database
  // Event code is used to qualify all tables so we can handle multiple regionals in the same database
  // Event code is also used on a number of Blue Alliance API calls.  Blue Alliance loader won't work without it.
  //
  // Confirms event code with Blue Alliance data, Then sets in our database.
  //

  require "page.inc";
  require "bluealliance.inc";

  global $sys_event_id;
  global $tba_AppId;
  global $tba_error;
  global $connection;
  global $tba_history_to_history;


  $connection = dbsetup();


  $query = "select teamnum from teambot";

   if (!($result = @mysqli_query ($connection, $query)))
        dbshowerror($connection, "die");
   while ($row = mysqli_fetch_array($result))
   {
	  // get data
	  if (! ($tba_response = tba_getdata("http://www.thebluealliance.com/api/v2/team/frc{$row['teamnum']}/history/events", TRUE)))
		print "API returned " . $tba_error['message'] . "<br>\n";
	  else
	  {
		// loop through each type of stat in map function
		foreach($tba_history_to_history as $tag=>$column)
		{
		  print $tag . ":";
		  foreach($tba_response->body as $event)
		  {
			$tba_dbarray = array("teamnum"=>$row["teamnum"]);
			$tba_dbarray = tba_mapfields($tba_history_to_history, $event, $tba_dbarray);
			$stuff = array("teamnum"=>$row["teamnum"], "event_id"=>$tba_dbarray["event_id"]);
			tba_updatedb("team_history", $stuff, $tba_dbarray);
		  }
		  print "<br>\n";
		}

		// commit
		if (! (@mysqli_commit($connection) ))
		  dbshowerror($connection, "die");

		// Inform user
		print "&nbsp;&nbsp;&nbsp; ... stats loading complete.<br>\n";

		return(TRUE);

	  } // end of else from REST query

  }

  return(TRUE);


?>

  require "page.inc";
  // header and setup

  $connection = dbsetup();


  $query = "select teamnum from teambot";

   if (!($result = @mysqli_query ($connection, $query)))
        dbshowerror($connection, "die");
   while ($row = mysqli_fetch_array($result))
   {
       print $row['teamnum'] . "\n";

    }


?>