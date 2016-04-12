<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - Add/edit new match
  //
  require "page.inc";

  // header and setup
  pheader("Add / Edit Match");
  $connection = dbsetup();


  // get variables if they exist
  if (isset($_GET["type"])) $type = $_GET["type"];
  if (isset($_GET["matchnum"])) $matchnum = $_GET["matchnum"];
  $edit=$_GET["edit"];

  // define lock array, fields arrays
  // not needed -- inserts only
  //  note: date will be removed before being fed in SQL
  $match_fields = array("type", "matchnum", "final_type", "scheduled_utime", "date");

  // handle update if returning from edit mode

  // lock tables if in edit mode
  // Not needed in new/insert situation
  // if ($edit) dblock($dblock,"lock");  // lock row with current user id


  if ($edit == 2)
  {

  	// load operation
  	if ( $_POST[op] == "Save" )
	{
  		// check row
  		// dblock($dblock,"check");

  		// get teams and validate existance
		$teams = alliances_load( "post" );
  	    $valid_return = teams_validate ( $teams );

 		// insert into match_instance

		// load form fields
		$formfields = array_merge (array ("event_id"=>$sys_event_id), fields_load("post", $match_fields));

		// convert time to utime
		$formfields['scheduled_utime'] = strtotime($formfields['date'] . " " . $formfields['scheduled_utime']);
		// remove date
		unset($formfields['date']);
		// upper case and test match type
		$formfields['type'] = strtoupper ($formfields['type']);
		if (! (in_array($formfields['type'], ["P","Q","F"])))
		  showerror("Match type must be one of P, Q, F","die");

		$query = "insert into match_instance (" . fields_insert("fieldname", $formfields)
     			. ") values (" . fields_insert("insert", $formfields) . ")";
        if (debug()) print "<br>DEBUG-matchnew: " . $query . "<br>\n";

		// process query
		if (! (@mysqli_query ($connection, $query) ))
			dbshowerror($connection, "die");


		// insert into match_team_alliance
		foreach (array("R", "B") as $color)
		{
			$query = "insert into match_instance_alliance (event_id, type, matchnum, color) values ("
			. fields_insert("insert", $formfields, array("event_id", "type", "matchnum"))
			. ", '{$color}')";
			if (debug()) print "<br>DEBUG-matchnew: " . $query . "<br>\n";
		 	if (! (@mysqli_query ($connection, $query)))
				dbshowerror($connection, "die");
		}

		// insert teams
		foreach (array("Red", "Blue") as $color)
			{
				$teamcnt=0;
				while ($teamcnt++ < 3)
					{
					  $query = "insert into match_team (event_id, type, matchnum, teamnum, color) values ("
						. fields_insert("insert", $formfields, array("event_id", "type", "matchnum"))
						. ", {$teams[$color][$teamcnt]}, '" . substr($color,0,1) . "')";
					  if (debug()) print "<br>DEBUG-matchnew: " . $query . "<br>\n";

					  if (! (@mysqli_query ($connection, $query)))
						dbshowerror($connection, "die");
					}
			}

		// commit
		if (! (@mysqli_commit($connection) ))
			dbshowerror($connection, "die");

		// notify user
		print "<br><b>Match {$formfields['type']}-{$formfields['matchnum']} added to match listings.</b><br><br>\n";
	}

	// abandon lock
	// dblock($dblock,"abandon");

    // update completed
    $edit = 0;
  }


// if edit, start edit
if ($edit) print "<form method=\"POST\" action=\"/matchnew.php?edit=2\">\n\n";

print "<a href=\"{$base}\">Return to Home</a><br><br>";


  // if $edit show buttons
  if ($edit)
  	 print "<INPUT TYPE=\"submit\" name=\"op\" VALUE=\"Save\" ALIGN=middle BORDER=0>\n"
	. "&nbsp;<INPUT TYPE=\"submit\" name=\"op\" VALUE=\"Cancel\" ALIGN=middle BORDER=0>\n";
  else
  	 print "<a href=\"/matchnew.php?edit=1\">Edit this page</a>\n";

  print "<table valign=\"top\">\n";

  // field options
  $options["tr"] = TRUE;  // add tr tags
  // set today as default date
  $today = date('Y-m-d');
  $row = array("date"=>$today);

  print tabtextfield($edit,$options,$row,"type","Type (P=Practice, Q=Qualifying, F=Final):",2,2)
  . tabtextfield($edit,$options,$row,"matchnum","Match Number:",4,4)
  . tabtextfield($edit,$options,$row,"final_type","Final Type (Q=Quarter,S=Semi,F=Final):",1,1)
  . tabtextfield($edit,$options,$row,"date","Scheduled Date:",10,10)
  . tabtextfield($edit,$options,$row,"scheduled_utime","Scheduled Time (24HH:MM):",5,5)
  ; // end of print

  print "<tr>&nbsp;</tr><tr>&nbsp;</tr>";
  print "<tr><td>Red Alliance:</td><td>";
  alliancefield ("Red");
  print "</td></tr>";

  print "<tr><td>Blue Alliance:</td><td>";
  alliancefield ("Blue");
  print "</td></tr></table><br>";



  // if $edit show buttons
  if ($edit)
  	 print "<INPUT TYPE=\"submit\" name=\"op\" VALUE=\"Save\" ALIGN=middle BORDER=0>\n"
	. "&nbsp;<INPUT TYPE=\"submit\" name=\"op\" VALUE=\"Cancel\" ALIGN=middle BORDER=0>\n";
  else
  	 print "<a href=\"/matchnew.php?edit=1\">Edit this page</a>\n";


  if ($edit) print "\n</form>\n";

?>


<?php
   pfooter();
 ?>
