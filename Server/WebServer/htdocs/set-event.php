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

  // header and setup
  pheader("Set System Event Code");
  $connection = dbsetup();

  // get variables if they exist
  if (isset($_GET["new_sys_event_id"])) $new_sys_event_id = $_GET["new_sys_event_id"];
  $edit=$_GET["edit"];

  // put variables if they exist
  if (isset($_POST["listyear"]))
  	$listyear = $_POST["listyear"];
  else
  	$listyear = date("Y");  // default to this year if not set

  if (isset($_POST["new_sys_event_id"]))
  	$new_sys_event_id = $_POST["new_sys_event_id"];

  if (isset($_POST["op"]))
  	$op = $_POST["op"];

  // Edit key:
  // Edit not defined: list form
  // Edit==2: Commit and confirm action complete
  // Edit==1: open edit event id
  // Edit==3: List regions for year $listyear
  // Edit==4: Confirm setting regional.  If a change in regional, go to edit 5
  // Edit==5: Double-confirm setting region

  // change data as needed

  // edit == 2 -- commit
  if ($edit == 2)
  {
    if ($op == "Yes")
    {
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

      // map fields from response
      $tba_dbarray = tba_mapfields($tba_event_to_event, $tba_response->body, "");

      // update event data in event table
      tba_updatedb("event", array ("event_id"=>$new_sys_event_id), $tba_dbarray);

      // set system event ID
      $query = "update system_value set value = '{$new_sys_event_id}' where skey = 'sys_event_id'";
      if (! (@mysqli_query ($connection, $query) ))
        dbshowerror($connection, "die");

      // commit
      if (! (@mysqli_commit($connection) ))
          dbshowerror($connection, "die");

      // message user
      print "<br><br><b>System Event ID now set to {$new_sys_event_id}<br><br>\n\n";

      // load default event_id and name for this instance from database
      $query="select value, name from system_value, event where skey = 'sys_event_id' and event_id = value";
      if (! ($result = @mysqli_query ($connection, $query) ))
  	    dbshowerror($connection, "die");
      $row = mysqli_fetch_array($result);
      $sys_event_id = $row["value"];
      $sys_event_name = $row["name"];

    }
    else
      $edit="";
  }


  // edit 4 or 5, load new event id info
  if (($edit == 4) || ($edit == 5))
  {
    // ID must exist
    if ($new_sys_event_id == "")
    {
      showerror("Sys Event ID cannot be NULL");
      $edit="";
    }
    elseif (($edit == 5) && ($op != "Yes"))
      $edit="";
    else
    {
      try
      {
        $tba_url = "http://www.thebluealliance.com/api/v2/event/{$new_sys_event_id}";
          $tba_response = \Httpful\Request::get($tba_url)
             ->addHeader('X-TBA-App-Id',$tbaAppId)
             ->send();

      } catch (Exception $e)
      {
        showerror("Caught exception from Blue Alliance: " . $e->getMessage());
        showerror("Event ID '{$new_sys_event_id}' probably does not exist or server is not connected.");
        $edit="";
      }

      // set new variables
      $new_sys_event_name = $tba_response->body->short_name;
      $new_sys_event_year = $tba_response->body->year;
    }
  }

  // end of edit state processing

  // get sys_event_id year for listing
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


  //
  // Page formatting
  //

  print "<a href=\"{$base}\">Return to Home</a><br>\n";

  // format inside a table
  print "<br><table border=\"0\">\n<tr>\n";
  print "<td>Current System Event Code:</td>\n";

  // wrap around appropriate buttons on event code field
  if (($edit == 1) || ($edit == 3))
  {
    print "<td>\n<form method=\"POST\" action=\"/set-event.php?edit=4\">\n";
    print "<input type=\"text\" name=\"new_sys_event_id\" size=8 maxlength=8 value=\"{$sys_event_id}\">\n";
    print "&nbsp;<INPUT TYPE=\"submit\" name=\"op\" VALUE=\"Set New Event ID\" ALIGN=middle BORDER=0>\n";
    print "</form>";
  }
  else
    print "<td>{$sys_event_id}";

  // add edit button
  if ($edit == "")
  {
    print "<td>\n<form method=\"POST\" action=\"/set-event.php?edit=1\">\n";
    print "&nbsp;<INPUT TYPE=\"submit\" name=\"op\" VALUE=\"Change System Event ID\" ALIGN=middle BORDER=0>\n";
    print "</form>";
  }
  // finish row
  print "</td></tr>\n";

  // rest of table
  print "<tr><td>Current Event Name:</td><td>{$sys_event_name}</td></tr>\n";
  print "<tr><td>Current Event Year:</td><td>{$sys_event_year}</td></tr>\n";

  // show new ID if in confirm mode
  if (($edit == 4) || ($edit == 5))
  {
    print "<tr><td>New Event ID:</td><td>{$new_sys_event_id}</td></tr>\n";
    print "<tr><td>New Event Name:</td><td>{$new_sys_event_name}</td></tr>\n";
    print "<tr><td>New Event Year:</td><td>{$new_sys_event_year}</td></tr>\n";
  }

  // end of display table
  print "</table>\n";

   print "<br><br>\n";


  // confirm mode
  if (($edit == 4) || ($edit == 5))
  {
    if ($edit == 5) print "<b><font color=\"red\">Double-checking:</font><br>\n";
    print "<form method=\"POST\" action=\"/set-event.php?edit=";
    if ($edit == 5) print "2"; else print "5";
    print "&new_sys_event_id={$new_sys_event_id}\">\n";
    print "<b>Are you sure you want to change/set the System Event ID to the new ID above?</b>\n";
    print "<INPUT TYPE=\"submit\" name=\"op\" VALUE=\"Yes\" ALIGN=middle BORDER=0>\n";
    print "<INPUT TYPE=\"submit\" name=\"op\" VALUE=\"No\" ALIGN=middle BORDER=0>\n";
    print "</form>\n";
    print "<br>\n";
  }
  else
  {
    // show entry for year
    print "<form method=\"POST\" action=\"/set-event.php?edit=3\">\n";
    print "&nbsp;&nbsp;&nbsp;\n";
    print "Listing year: <input type=\"text\" name=\"listyear\" size=4 maxlength=4 value=\"{$listyear}\">\n";
    print "&nbsp;<INPUT TYPE=\"submit\" name=\"op\" VALUE=\"List Events\" ALIGN=middle BORDER=0>\n";
    print "</form>\n";
  }


  // Provide listing if needed
  if ($edit == 3)
  {
    print "<br><br>\n";

    // get data
    try
    {
      $tba_url = "http://www.thebluealliance.com/api/v2/events/{$listyear}";
      $tba_response = \Httpful\Request::get($tba_url)
         ->addHeader('X-TBA-App-Id',$tbaAppId)
         ->send();
    } catch (Exception $e)
    {
    print "Exception";
       showerror("Caught exception from Blue Alliance: " . $e->getMessage());
       return;
    }

    print "<table border=2>\n<tr><th>EventID</th><th>Year</th><th>Event Name</th><th>Location</th></tr>\n";

    // loop through object
    foreach($tba_response->body as $key=>$value)
    {
      print "<tr><td>";
      print $value->key;
      print "</td><td>";
      print $value->year;
      print "</td><td>";
      print $value->short_name;
      print "</td><td>";
      print $value->location;
      print "</td></tr>\n";
    }

    // close table
    print "</table>\n";

  }

  print "<br><br><a href=\"{$base}\">Return to Home</a><br>\n";

  pfooter();
 ?>