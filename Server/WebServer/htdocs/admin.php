<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System
  // Blue Alliance Data Loader
  //
  // Download and update various forms of Blue Alliance data
  //

  require "page.inc";

  // header and setup
  pheader("Administrative Functions");
  $connection = dbsetup();

  // if not administrator, display error.  Otherwise show admin section.
  if (! $admin)
    print "<h3>You must be an administrator to use this page.</h3>\n";
  else
  {

   // get variables if they exist
   if (isset($_GET["op"])) $op = $_GET["op"]; else $op = "";
   $edit=$_GET["edit"];


   //
   // Page formatting
   //

   print "<a href=\"{$base}\">Return to Home</a><br>\n";

   print "
   <ul>
   <li><a href=\"/user.php\">User Maintenance</a></li>
   </ul>
   &nbsp;&nbsp;&nbsp;&nbsp;<b>Data Loading and Retreival:</b>
   <ul>
   <li><a href=\"/set-event.php\">Set system competition event code</a></li>
   <br>
   <li><a href=\"/bluealliance.php\">Get updates from Blue Alliance</a></li>
   <br>
   <li><a href=\"/scheduleimport.php\">Import Schedule, usually used for practice matches</a></li>
   </ul>

   &nbsp;&nbsp;&nbsp;&nbsp;<b>Data Verification:</b>
   <ul>
   <li><a href=\"/fix-db-structure.php\">Test and fix database structure</a><br>&nbsp;(should be run at start of competition)</li>
   <br>
   <li><a href=\"/verify-params.php\">Verify custom parameters from config file</a></li>
   </ul>

   </ul>
   &nbsp;&nbsp;&nbsp;&nbsp;<b>Messaging:</b>
   <ul>
   <li><a href=\"/messagesend.php\">Send a Message to Field</a></li>
   <br>
   <li><a href=\"/messagerecv.php\">Receive a Message From Field</a></li>
   </ul>

   &nbsp;&nbsp;&nbsp;&nbsp;<b>Documentation System:</b>
   <ul>
   <li><a href=\"/admin.php\">Create document</a></li>
   <br>
   <li><a href=\"/admin.php\">Report on document</a></li>
   </ul>

   "; // end of print

   print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Software Version: {$release_version}\n";


  } // end of "if admin" qualification

  print "<br><br><a href=\"{$base}\">Return to Home</a><br>\n";

  pfooter();
 ?>