<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - Team Info / Robot page
  //   (should really be named teambotinfo)
  //
  require "page.inc";

  $pagename="/teaminfo.php";

  // set first link in navigation area, page-dependent
  //   not pretty - $nav1_before is before the $teamnum insertion, $nav1_after is after
  $nav1_before = "<a href=\"/teaminfocompile.php?teamnum=";
  $nav1_after = "\">Compile Match Evaluations</a><br>";


  //
  // form setup
  //

  // get variables
  $teamnum=$_GET["teamnum"];
  $edit=$_GET["edit"];

  // header and setup  -- use connection first so we can get "needs eval"
  $connection = dbsetup();

  pheader("{$teamnum} - Team Robot Info", "titleonly");


  // Set up sub...inc parameters required
  //  $teamnum
  //  $page_event_id (event_id used in this page)
  //  $page_allow_edits -- TRUE show edit buttons, FALSE does not
  //  $page_heading -- heading displayed on page
  //
  $page_event_id = $sys_event_id;
  $page_allow_edits=TRUE;
  $page_heading = "Team Robot Info - ";

  //
  // call shared team info header
  //
  require "teaminfoheading.inc";

  //
  // main info portion of page
  //

  require "teaminfofields.inc";

  // close the form if in edit mode
  if ($edit) print "\n</form>\n";

	showupdatedby($dblock);

	pfooter();

?>