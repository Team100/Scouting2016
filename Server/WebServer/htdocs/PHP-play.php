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
  include ('lib/httpful.phar');

  // header and setup

  $connection = dbsetup();


  	$listyear = date("Y");  // default to this year if not set

$new_sys_event_id='2015abca';
$new_sys_event_id='2016ausy';
$new_sys_event_id='2016calb';



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



