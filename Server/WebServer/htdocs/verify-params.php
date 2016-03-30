<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System
  // Verify parameters
  //
  // Performs simple tests on parameters
  //

  require "page.inc";
  require "bluealliance.inc";

  // header and setup
  pheader("Verifying Customer Parameter Configuration");
  $connection = dbsetup();

 // if not administrator, display error.  Otherwise show admin section.
 if (! $admin)
   print "<h3>You must be an administrator to use this page.</h3>\n";
 else
 {


  //
  // Walk through parameters and display to user
  //


  print "<a href=\"{$base}\">Return to Home</a>\n";
  print "&nbsp;&nbsp;&nbsp; <a href=\"/admin.php\">Sys Admin</a><br>\n";

  print "\n<br>\nLook through the parameter listing below for what the system has read and interpretted.\n";
  print "
    <p>Please look for the following:
    <ul>
    <li>Order number of list matches params numbers in files
    <li>Arrays are well-formed with no missing components
    </ul>
    "; // end of print

  // loop through each param group
  foreach(array('Play','Match') as $paramgroup)
  {
    // initialize
    $fcnt=0;

    foreach($dispfields[$paramgroup] as $key)
    {
      print "<br>" . $fcnt++ . " {$paramgroup}:";
      print_r($key);
    }
    print "<br>";
  }


 } // end of "if admin" qualification


  print "<br><br><a href=\"{$base}\">Return to Home</a>\n";
  if ($admin) print "&nbsp;&nbsp;&nbsp; <a href=\"/admin.php\">Sys Admin</a>\n";
  print "<br>\n";

  pfooter();
 ?>