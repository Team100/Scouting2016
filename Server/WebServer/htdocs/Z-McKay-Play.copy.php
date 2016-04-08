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

    global $sys_event_id;
    global $tba_AppId;
    global $tba_error;
    global $connection;
    global $tba_stats_to_teambot;

    // get data
    if (! ($tba_response = tba_getdata("http://www.thebluealliance.com/api/v2/event/{$sys_event_id}/stats", FALSE)))
      print "API returned " . $tba_error['message'] . "<br>\n";
    else
    {
      // loop through each type of stat in map function
      foreach($tba_stats_to_teambot as $tag=>$column)
      {
        print $tag . ":";
        foreach($tba_response->body->$tag as $teamnum=>$value)
        {
          $id_array = array("event_id"=>$sys_event_id, "teamnum"=>$teamnum);
          tba_updatedb("teambot", $id_array, array($column=>$value));
          print $teamnum . ", ";
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

    return(TRUE);


exit;





  	$listyear = date("Y");  // default to this year if not set

$new_sys_event_id='2015abca';
$new_sys_event_id='2016ausy';
$new_sys_event_id='2016calb';

print "\n";


        foreach(array("blue", "red") as $colorkey)
        {
          if ($colorkey == "blue") $color='B'; else $color='R';
print $colorkey . $color;
}

exit;

$tba="frc0111";

sscanf($tba, "frc%d", $teamnum);

$match="2010sc_qm20";

$end = strstr($match, '_');

preg_match('/[0-9]*$/', $end, $matchnumarray);
$matchnum=$matchnumarray[0];

$complevel= substr($end, 1, strpos($end, $matchnum)-1);

// print_r($matches);

$end = strstr($match, '_');

print "end" . $matchnum;

print "\n";
print "match" . $complevel . " between " . $matchnum;

print "\n";

exit;
//$end = "_qm33";

sscanf($end, "_%s%d", $complevel, $matchnum);

print "\n";
print "match" . $complevel . " between " . $matchnum;
print "\n";

print "teamnum" . $teamnum;

print "\n";

exit;



  // get sys_event_id year
  if ($sys_event_id != "")
    {
      $query="select year from system_value, event where skey = 'sys_event_id' and event_id = value";
      if (! ($result = @mysqli_query ($connection, $query) ))
         dbshowerror($connection, "die");
      $row = mysqli_fetch_array($result);
      $sys_event_year = $row["year"];
    }
  else
    $sys_event_year = "";


      try
        {
          $tba_url = "http://www.thebluealliance.com/api/v2/event/{$new_sys_event_id}";
          $tba_response = \Httpful\Request::get($tba_url)
             ->addHeader('X-TBA-App-Id',$tbaAppId)
             ->send();

        } catch (Exception $e)
        {
           showerror("Caught exception from Blue Alliance: " . $e->getMessage());
           return;
        }


print_r($tba_event_to_event);

// print_r($tba_response);

$tba_dbarray = tba_mapfields($tba_event_to_event, $tba_response->body, "");

print "\n\n";
print_r($tba_dbarray);

tba_updatedb("event", array ("event_id"=>$new_sys_event_id), $tba_dbarray);

			// commit
			if (! (@mysqli_commit($connection) ))
				dbshowerror($connection, "die");
exit;



while($row = mysqli_fetch_array($result))
  	{
  		$teamnum=$row["teamnum"];

  		// load team array
		$team[$teamnum]=$row;

		// load sort array
		$teamsrank[$teamnum]=$row["rank_" . $sort];

	}

		$query = "insert into match_instance (" . fields_insert("fieldname", $formfields)
     			. ") values (" . fields_insert("insert", $formfields) . ")";

		// process query
		if (! (@mysqli_query ($connection, $query) ))
			dbshowerror($connection, "die");



  // edit 4 or 5, load new event id info

  $tbaurl = "http://www.thebluealliance.com/api/v2/event/{$new_sys_event_id}";
  $tbaresponse = \Httpful\Request::get($tbaurl)
     ->addHeader('X-TBA-App-Id',$tbaAppId)
     ->send();



