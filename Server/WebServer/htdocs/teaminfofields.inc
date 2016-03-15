<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - Team Info / Robot page -- Sub
  //
  //  "Sub" form shared by other top-level forms
  //

  //require "page.inc";

  //
  // main page fields
  //

  // field options
  $options["tr"] = FALSE;  // no tr tags

  print "
  <!--- table for all fields block --->
  <table><tr valign=\"top\">
    <td>

  <!--- table for display data --->
  <table border=\"1\" valign=\"top\">
  <tr><th></th><th>Rating<br>(-3 to +3</th><th>Rank</th><tr>\n";

  // two fields - rating and rank
  $options["notag"]=FALSE;
  print "<tr>" . tabtextfield($edit,$options,$row, "rating_overall","Overall:&nbsp",4);
  $options["notag"]=TRUE;
  print tabtextfield($edit,$options,$row, "rank_overall","Overall:&nbsp",4) . "</tr>\n";

  $options["notag"]=FALSE;
  print "<tr>" . tabtextfield($edit,$options,$row, "rating_overall_off","Overall Offensive:&nbsp",3) . "</tr>\n";
  print "<tr>" . tabtextfield($edit,$options,$row, "rating_overall_def","Overall Defensive:&nbsp",3) . "</tr>\n";

  // print only if working if fields positions matter
  if ($field_positions === TRUE)
  {
	  // two fields - rating and rank
	  $options["notag"]=FALSE;
	  print "<tr>" . tabtextfield($edit,$options,$row, "rating_pos1","Position #1:&nbsp",3);
	  $options["notag"]=TRUE;
	  print tabtextfield($edit,$options,$row, "rank_pos1","Position #1:&nbsp",4) . "</tr>\n";

	  // two fields - rating and rank
	  $options["notag"]=FALSE;
	  print "<tr>" . tabtextfield($edit,$options,$row, "rating_pos2","Position #2:&nbsp",3);
	  $options["notag"]=TRUE;
	  print tabtextfield($edit,$options,$row, "rank_pos2","Position #2:&nbsp",4) . "</tr>\n";

	  // two fields - rating and rank
	  $options["notag"]=FALSE;
	  print "<tr>" . tabtextfield($edit,$options,$row, "rating_pos3","Position #3:&nbsp",3);
	  $options["notag"]=TRUE;
	  print tabtextfield($edit,$options,$row, "rank_pos3","Position #3:&nbsp",4) . "</tr>\n";
  }

  // end of first block of variables
  print "</table></td><td>&nbsp;&nbsp;</td><td>\n";

  // Play variables
  print "
  <b>Play and Pit Evaluation</b>
  <!--- Layout table --->
  <table><tr valign=\"top\"><td>
  <table border=\"1\" valign=\"top\">
   ";

  // get play variables
  $options["tr"]=TRUE;
  $options["notag"]=FALSE;
  $options["pagebreak"]=3;
  $options["pagebreakstring"]="\n</table>\n</td><td>\n<table border=\"1\">\n";
  print tabparamfields($edit, $options, $row, "Play");

  // end of blocks of data and two layouts
  print "\n</table>\n</td></tr></table>\n</td></tr></table>";


  // analysis table format
  $options["notag"]=FALSE;
  print "<table>
  <tr>
  <td>";

  print tabtextarea($edit,$options,$row, "with_recommendation","With Recommendation:",4,100)
  . tabtextarea($edit,$options,$row, "against_recommendation","Against Recommendation:",4,100)
  . tabtextarea($edit,$options,$row, "offense_analysis","Offensive Analysis:",4,100)
  . tabtextarea($edit,$options,$row, "defense_analysis","Defensive Analysis:",4,100)
// DEPRICATE  . tabtextarea($edit,$options,$row, "robot_analysis","Overall Robot Analysis:",4,100)
  . tabtextarea($edit,$options,$row, "driver_analysis","Driver Analysis:",4,100);

  // print only if working in position mode
  if ($field_positions === TRUE)
  {
  	print
	  tabtextarea($edit,$options,$row, "pos1_analysis","Position 1 Analysis:",4,100)
	  . tabtextarea($edit,$options,$row, "pos2_analysis","Position 2 Analysis:",4,100)
	  . tabtextarea($edit,$options,$row, "pos3_analysis","Position 3 Analysis:",4,100);
  }
  
  // close table
  print "</td></tr></table>";

  // end of team info data block

    // add edit link or submit button
  print dblockshowedit($edit, $dblock, $editURL) . "\n";


//
// end of page include file
//
?>
