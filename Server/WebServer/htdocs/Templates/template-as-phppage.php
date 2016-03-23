<?php
  // $Revision: 1.2 $
  // $Date: 2010/03/20 13:14:19 $
  //
  // Competition System - page
  //
  //
  //

  require "page.inc";
  // get variables


  pheader("Template", "titleonly");
  $connection = dbsetup();

  // get variables if they exist
  if (isset($_GET["op"])) $op = $_GET["op"]; else $op = "";
  $edit=$_GET["edit"];

  // define lock array, fields arrays
  // not needed -- inserts only
  $match_fields = array("type", "matchnum", "final_type", "scheduled_time", "actual_time");

  // handle update if returning from edit mode

  // lock tables if in edit mode
  // if ($edit) dblock($dblock,"lock");  // lock row with current user id

  // process lock

  //   	if ( $_POST[op] == "Save" )

  //
  // Page formatting
  //

  print "<a href=\"{$base}\">Return to Home</a><br>\n";

  // format inside a table
  print "<br><table border=\"0\">\n<tr>\n";



  print "<br><br><a href=\"{$base}\">Return to Home</a><br>\n";

  pfooter();
 ?>