<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - Team Info / Robot page
  //   (should really be named teambotinfo)
  //
  require "page.inc";

  $pagename="/teamotherevents.php";

  // set first link in navigation area, page-dependent
  //   not pretty - $nav1_before is before the $teamnum insertion, $nav1_after is after
  $nav1_before = "<a href=\"/teaminfo.php?teamnum=";
  $nav1_after = "\">Return to Team Info</a><br>";

  //
  // form setup
  //

  // get variables
  $teamnum=$_GET["teamnum"];
  $edit=$_GET["edit"];

  // header and setup  -- use connection first so we can get "needs eval"
  $connection = dbsetup();

  pheader("{$teamnum} - Team Other Season Events", "titleonly");


  //
  // find other season events
  //
  $query = "select teambot.event_id event_id, event.name event_name from teambot, event "
       . " where teamnum = {$teamnum} and teambot.event_id != '{$sys_event_id}'"
       . " and teambot.event_id = event.event_id";

  if (debug()) print "<br>DEBUG-teamotherevents: " . $query . "<br>\n";
  if (!($event_result = @ mysqli_query ($connection,$query)))
    dbshowerror($connection);

  $found=FALSE;
  // get rows
  while($event_row = mysqli_fetch_array($event_result))
  {
    $found=TRUE;

    // Set up sub...inc parameters required
    //  $teamnum
    //  $page_event_id (event_id used in this page)
    //  $page_allow_edits -- TRUE show edit buttons, FALSE does not
    //  $page_heading -- heading displayed on page
    //
    $page_event_id = $event_row['event_id'];
    $page_allow_edits=FALSE;
    $page_heading = "Event: {$event_row['event_name']}, Team  - ";

    //
    // call shared team info header
    //
    require "teaminfoheading.inc";

    //
    // main info portion of page
    //

    require "teaminfofields.inc";

    print "<br><hr>\n";

  }

  if (! ($found))
  {
    print "<h3>No other events found for team {$teamnum}</h3>\n";
    print "<a href=\"{$base}\">Return to Home</a>\n";
  }


	pfooter();

?>