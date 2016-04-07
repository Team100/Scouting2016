<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - Team Info / Robot page -- Sub
  //
  //  "Sub" form shared by other top-level forms
  //

  //
  // main page fields
  //

  //
  // get side-by-side data, load into $matches array so it can be printed
  //
  $query = "select * from match_team where event_id = '{$sys_event_id}' and teamnum = {$teamnum}
            order by field(type,'F','Q','P'), matchnum DESC";
  
  if (debug()) print "<br>DEBUG-teamfinfosidebyside: " . $query . "<br>\n";
  if (! ($result = @ mysqli_query ($connection, $query)))
       dbshowerror($connection, "die");

  $cnt=0;
  while($row = mysqli_fetch_array($result))
    $matches[$cnt++] = $row;

  //
  // build matches display array db_column=>display
  //
  $match_fields = array(
    "rating_offense"=>"Offense Rating",
    "rating_defense"=>"Defense Rating"
    );
    
  // add Matchfields
  foreach($dispfields['Match'] as $fieldnum=>$matcharray)
    $match_fields = array_merge($match_fields, array("MatchField_" . $fieldnum => $matcharray['display']));
 

  //
  // field options
  //

  // field options
  $options["tr"] = FALSE;  // no tr tags

  print "
  <!--- table for all fields block --->
  <table><tr valign=\"top\">
    <td>

  <!--- table for display data --->
  <table border=\"1\" valign=\"top\">
  <tr><th></th><th>Rating<br>(0 - 9)</th><th>Rank</th><tr>
  "; // end of print

  // two fields - rating and rank
  $options["notag"]=FALSE;
  print "<tr>" . tabtextfield($edit,$options,$row, "rating_overall","Overall:&nbsp",4);
  $options["notag"]=TRUE;
  print tabtextfield($edit,$options,$row, "rank_overall","Overall:&nbsp",4) . "</tr>\n";

  $options["notag"]=FALSE;
  print "<tr>" . tabtextfield($edit,$options,$row, "rating_overall_off","Overall Offensive&nbsp",3) . "</tr>\n";
  print "<tr>" . tabtextfield($edit,$options,$row, "rating_overall_def","Overall Defensive&nbsp",3) . "</tr>\n";

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

  // end of ranking block of variables
  print "</table>\n\n";
  
  //
  // Display Play variables
  print "
  <br>
  <b>Play and Pit Evaluation</b>
  <!--- Layout table --->
  <table><tr valign=\"top\"><td>
  <table border=\"1\" valign=\"top\">
   ";

  // get play variables
  $options["tr"]=TRUE;
  $options["notag"]=FALSE;
//  $options["pagebreak"]=0;
//  $options["pagebreakstring"]="\n</table>\n</td><td>\n<table border=\"1\">\n";
  print tabparamfields($edit, $options, $row, "Play");



  // end of blocks of data and two layouts
  print "\n</table>\n</td></tr></table>\n";


  // stats block
  print "
  <br>
  
  <!--- table for stats data --->
  <table border=\"1\" valign=\"top\">
  "; // end of print
  
  foreach($stats_columns as $column=>$col_def)
  {
    // check for format
    if ($col_def['format'] != "") 
      $show = sprintf($col_def['format'], $row[$column]);
    else
      $show = $row[$column];
    print "<tr><td>{$col_def['display']}</td><td>{$show}</td></tr>\n";
  }


  // end of first block/column of variables
//  print "</table>\n</td><td>&nbsp;&nbsp;</td><td>\n";
  print "</table>\n<br>\n";
 
 
  // 
  // New block of variables
  //

  print "
  <!--- table for display data --->
  <table border=\"1\" valign=\"top\">
  "; // end of print
  
  
  // add game-specific fields
  foreach($RankFields as $rankfield)
    if ($rankfield['display'] != NULL)
      print "<tr><td>{$rankfield['display']}</td><td>{$row[$rankfield['column']]}</td><tr>\n";

  // end of first block of variables
  print "</table></td>\n";

  
  //
  // display side-by-side listing
  //
  print "<td>&nbsp;&nbsp;&nbsp;</td><td>\n";
  
  print "
  <b>Match Evaluations</b>
  <!--- Layout table --->
  <table border=\"1\" valign=\"top\">
   ";
   
  // display column headings -- match ID
  print "<tr><th></th>\n";
  
  foreach($matches as $matchdata)
    print "<th>{$matchdata['type']}{$matchdata['matchnum']}</th>\n";

  print "</tr>\n";

  // add column per row of displayed data
  foreach($match_fields as $column=>$display)
    {
      print "<tr><td>{$display}</td>";
      
      // iterate matches
      foreach($matches as $matchdata)
        print "<td>{$matchdata[$column]}</td>";
      
      print "</tr>\n";
    }

  // end of match evaluations table
  print "</table>\n";
  // end of layout
  print "</td></tr></table>\n";


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
  print "</td></tr></table>\n";

  // end of team info data block

  //
  // add notes section:
  //
  
  print "<br><b>Notes from Matches:</b><br>\n";
  print "<table border=\"0\">\n";
  
  foreach($matches as $matchdata)
    print "<tr><td>{$matchdata['type']}{$matchdata['matchnum']}:</td><td>{$matchdata['notes']}</td></tr>\n";

  print "<table><br>\n";
  
  // end of notes section

    // add edit link or submit button
  print dblockshowedit($edit, $dblock, $editURL) . "\n";


//
// end of page include file
//
?>
