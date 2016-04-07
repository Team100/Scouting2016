<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System
  // Blue Alliance Data Automatic Loader
  //
  // Runs in background periodically and updates data needed at tournament
  //

  require "page.inc";
  require "bluealliance.inc";

  $connection = dbsetup();

  // check for server variable and abort if being run from a web server
  if ($_SERVER['SERVER'] !== NULL)
  {
    print "ERROR: Cannot be run from a web page\n";
    print "Aborting.";
    exit;
  }

  print "\nCompetition System Batch Run\n";
  print "Starting " . date('Y-m-d-H:i:s');


      // inform user
      print date('Y-m-d-H:i:s') . "Processing event matches...<br>\n";
      if (tba_get_matches())
        print "Blue Alliance operation successful.<br>\n";
      else
        print "Blue Alliance operation failed.  Please check errors.<br>\n";
      print "<br>";


      // stats first

      // inform user
      print date('Y-m-d-H:i:s') . "Retrieving event stats...<br>\n";
      if (tba_get_event_stats())
        print "Blue Alliance operation successful.<br>\n";
      else
        print "Blue Alliance operation failed.  Please check errors.<br>\n";
      print "<br>\n";

      // rankings
      // inform user
      print date('Y-m-d-H:i:s') . "Retrieving rankings...<br>\n";
      if (tba_get_event_rankings())
        print "Blue Alliance operation successful.<br>\n";
      else
        print "Blue Alliance operation failed.  Please check errors.<br>\n";
      print "<br>";


  pfooter();
 ?>