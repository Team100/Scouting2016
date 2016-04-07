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