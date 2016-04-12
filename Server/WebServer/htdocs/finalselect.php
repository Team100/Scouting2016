<?php
	// $Revision: 3.0 $
	// $Date: 2016/03/14 22:56:41 $
	//
	// Competition System - Select finals team
	//
	require "page.inc";

	// get variables
	$edit=$_GET["edit"];
	$sort=$_GET["sort"];
	$steal=$_GET["dblocksteal"];
	// set default if needed
	if ($sort == "") $sort="overall";

	// header and setup
	pheader("Finals Alliance Selection - " . $host_team_name);
	$connection = dbsetup();

	// define lock array, fields arrays
	$dblock = array(table=>"process_lock",where=>"lock_id = 'finals_selection'");
	//$table_team = array("name","nickname","org","location","students","website");

	//********
	// db lock custom:
	if($steal==1)//steal this table
		if (! (@mysqli_query ($connection, "update process_lock set locked='{$user}' where lock_id='finals_selection'") ))
			dbshowerror($connection, "die");

	if (! ($result=@mysqli_query ($connection, "select locked from process_lock where lock_id='finals_selection'") ))
		dbshowerror($connection, "die");
	$dbcontrol=null;
	$row = mysqli_fetch_array($result);
	$dbcontrol=$row["locked"];

	if($dbcontrol && $dbcontrol!=$user)
	{
		if($edit)
		{
			showerror("This table is being edited- steal the page if you wish to continue");
			print "<br>";
		}//editing without permision

		$edit=0;
	}//no permission, give the option to steal the page
	if(!$dbcontrol && $edit)
	{
		dblock($dblock, "lock");
		//if (! (@mysqli_query ($connection, "update process_lock set locked='{$user}' where lock_id='finals_selection'") ))
		//	dbshowerror($connection, "die");
		$dbcontrol=$user;
	}//take control if there is no control


	//********

	// handle update if returning from edit mode
	// edit:
	//   0 = no edit, read only
	//   1 = go into edit mode
	//   2 = save and return to non-edit mode
	//   3 = save and stay in edit mode
	//
	//   10 = sequential entering of teams, continue the name
	//   11 = clear table and enter teams from the beginning
	//   12 = enter teams just entered from 11
	//   13 = edit all teams in a grid
	//   14 = submit all teams from 13
	//	 15 = duplicate of 11 but does not delete the tables,
	//			allows user to keep data when in edit 11 and clicks on an ordering link

    // clear teams before starting an edit
	if($edit == 11 || $edit == 12)
	{
		if (! (@mysqli_query ($connection, "delete from alliance_team where event_id = '{$sys_event_id}'") ))
			dbshowerror($connection, "die");
		if (! (@mysqli_query ($connection, "delete from alliance where event_id = '{$sys_event_id}'") ))
			dbshowerror($connection, "die");
		if (! (@mysqli_query ($connection, "delete from alliance_unavailable where event_id = '{$sys_event_id}'") ))
			dbshowerror($connection, "die");

		for($i=1; $i<=8; $i++)
			if (! (@mysqli_query ($connection, "insert into alliance (event_id, alliancenum) values
				('{$sys_event_id}', {$i})") ))
				dbshowerror($connection, "die");
	}
	if($edit == 12)
	{
		$error_message="";
		$notentered=0;
		for($i=1; $i<=8; $i++)
		{
			$temp = $_POST["team".$i];
			if(isset($temp) && $temp!="")
			{
				$bad=0;//bad is 1 if temp is a repeat
				for($q=1; $q<$i; $q++)
					if($_POST["team".$q]==$temp)
						$bad=1;

				if (! ($result2 = @mysqli_query ($connection, "select * from teambot where event_id = '{$sys_event_id}' and teamnum={$temp}") ))
					dbshowerror($connection, "die");
				$row = @mysqli_fetch_array($result2);
				if(!($row))
				{
					$bad=1;
					if($error_message)
						$error_message="Team {$temp} does not exist<br>{$error_message}";
					else
						$error_message="Team {$temp} does not exist";
				}

				if($bad==0)
				{
					$query = "insert into alliance_team (event_id, alliancenum, teamnum, position) values
						('{$sys_event_id}', {$i}, {$temp}, 1)";

					if (! (@mysqli_query ($connection, $query) ))
						dbshowerror($connection, "die");

				}
				else
				{
					$notentered=1;

					if($error_message)
						$error_message="Duplicate entry: {$temp}<br>{$error_message}";
					else
						$error_message="Duplicate entry: {$temp}";
				}
			}
			else
				$notentered=1;
			//print "t{$i} {$temp} ";
		}
		if($notentered)
			$edit=11;
		else
			$edit=10;
	}
	if($edit == 14)
	{
		// clear existing entries and reset to fresh alliances
		$error_message="";
		if (! (@mysqli_query ($connection, "delete from alliance_team where event_id = '{$sys_event_id}'") ))
			dbshowerror($connection, "die");
		if (! (@mysqli_query ($connection, "delete from alliance where event_id = '{$sys_event_id}'") ))
			dbshowerror($connection, "die");
		if (! (@mysqli_query ($connection, "delete from alliance_unavailable where event_id = '{$sys_event_id}'") ))
			dbshowerror($connection, "die");

		// add alliance numbers
		for($i=1; $i<=8; $i++)
			if (! (@mysqli_query ($connection, "insert into alliance (event_id, alliancenum) values
				('{$sys_event_id}', {$i})") ))
				dbshowerror($connection, "die");

		for($i=1; $i<=8; $i++)
		{
			for($q=1; $q<=3; $q++)
			{
				$temp = $_POST["team".$i.$q];
				if(isset($temp) && $temp!="")
				{
					if (! ($result2 = @mysqli_query ($connection, "select * from teambot where event_id = '{$sys_event_id}' and teamnum={$temp}") ))
						dbshowerror($connection, "die");
					$row2 = @mysqli_fetch_array($result2);

					if(!($row2))
					{
						if($error_message)
							$error_message="Team {$temp} does not exist<br>{$error_message}";
						else
							$error_message="Team {$temp} does not exist";
					}//team does not exist
					else
					{
						// team does exist, add to database
						$query = "select * from alliance_team where event_id = '{$sys_event_id}' and teamnum={$temp}";
						if (!($result=@mysqli_query ($connection, $query) ))
							dbshowerror($connection, "die");
						$row = mysqli_fetch_array($result);

						if(!($row))
						{
							$query = "insert into alliance_team (event_id, alliancenum, teamnum, position) values
								('{$sys_event_id}', {$i}, {$temp}, {$q})";

							if (! (@mysqli_query ($connection, $query) ))
								dbshowerror($connection, "die");

							if($q>1 || (isset($_POST["team".$i."2"]) && $_POST["team".$i."2"]!=""))
							{
								$query = "insert into alliance_unavailable (event_id, alliancenum, teamnum, unavailable) values
									('{$sys_event_id}, {$i}, {$temp}, TRUE)";
								mysqli_query ($connection, $query);
								//this does not throw an error if there is a duplicate entry, same for whole page
							}//add to unavailable if not in the first column or has another team in the second column
						}//this team is not repeated in the table
						else
						{
							if($error_message)
								$error_message="Duplicate entry: {$temp}<br>{$error_message}";
							else
								$error_message="Duplicate entry: {$temp}";
						}
					}
				}
			}
		}
		$edit=1;
	}//enter all teams

	if($edit==15)
		$edit=11;


	// *******************************
	//
	// Start page
	//

	//** mode seclection links
	if($edit)
	{
		print "<a href=\"/finalselect.php?edit=11\">Start Sequential Selection</a> (erases current)&nbsp;&nbsp;&nbsp;\n";
		print "<a href=\"/finalselect.php?edit=10\">Continue Sequential Selection</a>&nbsp;&nbsp;&nbsp;\n";
		print "<a href=\"/finalselect.php?edit=13\">Edit all Teams</a>&nbsp;&nbsp;&nbsp;\n";
		print "<a href=\"/finalselect.php\">Continue/Cancel Editing</a><br>\n";

		if(!$dbcontrol)
			dblock($dblock, "lock");
	}//options for editing
	else if($dbcontrol==$user)
	{
		dblock($dblock, "abandon");
		$dbcontrol=null;
	}//remove your own control if not editing
	if(!$edit && !$dbcontrol)
		print "<a href=\"/finalselect.php?edit=1\">Edit this page</a><br>";
	else if($dbcontrol && $dbcontrol!=$user)
	{
		print "Locked by {$dbcontrol}- <a href=\"/finalselect.php?dblocksteal=1&edit=1\">!Steal the page!</a><br>\n";
	}//page stealing

	if($error_message)
		print "<br><font color=\"red\"><b>{$error_message}</font></b>\n";

    //************************************************
  	//Conrads Work:

	$error_message="";
	if($edit == 10)
	{
		// commit refusal:
		$refused=mysqli_real_escape_string($connection, $_POST["refused"]);
		if(isset($refused) && $refused!="")
		{
			if (! ($result2 = @mysqli_query ($connection, "select * from teambot where event_id = '{$sys_event_id}' and teamnum={$refused}") ))
				dbshowerror($connection, "die");
			$row2 = @mysqli_fetch_array($result2);

			if(!($row2))
			{
				if($error_message)
					$error_message="Team {$refused} does not exist<br>{$error_message}";
				else
					$error_message="Team {$refused} does not exist";
			}//team does not exist
			else
			{
				if($refused<0)
				{
					$refused=$refused*-1;
					if(!(mysqli_query ($connection, "delete from alliance_unavailable where event_id = '{$sys_event_id}' and teamnum = {$refused}")))
							dbshowerror($connection, "die");
				}//if enter a negative number remove that form the unavilable list
				else
				{
					$wrong=0;
					if(!(mysqli_query ($connection, "insert into alliance_unavailable (event_id, teamnum, refused)
						values ('{$sys_event_id}', {$refused}, true)")))
						$wrong=1;
					if(!(mysqli_query ($connection, "update alliance_unavailable set refused = true where teamnum = $refused")))
						$wrong=1;
					if($wrong)
						$error_message="Bad input for refusal";
				}
			}
		}//end of entering refusal; refusalinput

		//fill unavailable list, doing this before seems to remove errors:
		for($num = 1; $num <=2; $num++)//counter for how many teams are in the alliance
		{
			for($tnQ=1; $tnQ<=8; $tnQ++)
			{
				$tn=$tnQ;//only use $tn from here
				if($num==3)//counts backwards for the last team selection
					$tn = 9-$tn;

				if($num>=2)
				{
					if (! ($result = @ mysqli_query ($connection, "select teamnum from
						alliance_team where event_id = '{$sys_event_id}' and alliancenum = '{$tn}'") ))
						dbshowerror($connection, "die");

					while($row = mysqli_fetch_array($result))
					{
						mysqli_query ($connection, "insert into alliance_unavailable (event_id, teamnum, refused)
							values ('{$sys_event_id}', {$row['teamnum']}, false)");
						//print no errors because will probably insert duplicate data
					}
				}//add teams to unavailable list

				if(!($resultT = @ mysqli_query ($connection, "select * from alliance_team
					where event_id = '{$sys_event_id}' and alliancenum = {$tn} and position = {$num}") ))
					dbshowerror($connection, "die");

				$found=0;
				while($rowT = mysqli_fetch_array($resultT))
					$found = 1;
				if($found==0)
				{
					$num=4;
					break;
				}
			}
		}//end of filling the unavailable list


		//inputs entered data into tables, determines validity of input
		$prev=mysqli_real_escape_string($connection, $_POST["next"]);

		if(isset($prev) && $prev!="")
		{
			if (! ($result2 = @mysqli_query ($connection, "select * from teambot where event_id = '{$sys_event_id}' and teamnum={$prev}") ))
				dbshowerror($connection, "die");
			$row2 = @mysqli_fetch_array($result2);

			if(!($row2))
			{
				if($error_message)
					$error_message="Team {$prev} does not exist<br>{$error_message}";
				else
					$error_message="Team {$prev} does not exist";
			}//team does not exist
			else
			{
				for($num = 1; $num <=3; $num++)//counter for how many teams are in the alliance
				{
					for($tnQ=1; $tnQ<=8; $tnQ++)
					{
						$tn=$tnQ;//only use $tn from here
						if($num==3)//counts backwards for the last team selection
							$tn = 9-$tn;

						if(!($resultT = @ mysqli_query ($connection, "select * from alliance_team
							where event_id = '{$sys_event_id}' and alliancenum = {$tn} and position = {$num}") ))
							dbshowerror($connection, "die");

						$found=0;
						while($rowT = mysqli_fetch_array($resultT))
						{
							$found = 1;
						}
						if($found == 0)
						{
							$uCount=0;

							if($num==1)
							{
								$query = "select * from	alliance_unavailable where event_id = '{$sys_event_id}' and
								       teamnum = '{$prev}' and refused=false";
								if (! ($result = @ mysqli_query ($connection, $query) ))
									dbshowerror($connection, "die");
							}
							else
							{
								if (! ($result = @ mysqli_query ($connection, "select * from
									alliance_unavailable where event_id = '{$sys_event_id}' and teamnum = '{$prev}'") ))
									dbshowerror($connection, "die");
							}

							while($row = mysqli_fetch_array($result))
							{
								$uCount++;
							}

							if($uCount==0)
							{
								$query = "select * from alliance_team where event_id = '{$sys_event_id}' and teamnum = '{$prev}'";
								if (! ($result = @ mysqli_query ($connection, $query) ))
									dbshowerror($connection, "die");
								$alliancenum=0;
								while($row = mysqli_fetch_array($result))
								{
									$alliancenum=$row['alliancenum'];
								}

								if($num!=1)
									if($alliancenum!=0)
									{
										$alliancenum++;
										for(; $alliancenum<=8; $alliancenum++)
										{
											$newalliancenum=$alliancenum-1;
											if (! ($result = @ mysqli_query ($connection, "update alliance_team set alliancenum = {$newalliancenum}
												where alliancenum = '{$alliancenum}' and position = '1'") ))
												dbshowerror($connection, "die");
										}
									}//if a team is taken from the first column shift lower teams up
								$query = "delete from alliance_team where event_id = '{$sys_event_id}' and teamnum = '{$prev}'";
								if (! ($result = @ mysqli_query ($connection, $query) ))
									dbshowerror($connection, "die");

								$query = "insert into alliance_team (event_id, alliancenum, teamnum, position)
								          values ('{$sys_event_id}',{$tn}, {$prev}, {$num})";
									if (! ($result = @ mysqli_query ($connection, $query) ))
									dbshowerror($connection, "die");
								if($num>=2)
									mysqli_query ($connection, "insert into alliance_unavailable (event_id, teamnum, refused)
										values ('{$sys_event_id}', {$prev}, false)");
							}
							else
								$error_message="Team {$prev} is unavailable";

							//check no more:
							if($num==3 && $tn==1)//if teh table is full
								$edit=0;
							$num=4;
							break;
						}//found next slot
					}//alliance iterator
				}//size of alliance counter
			}//valid team
		}//if valid data is entered
		// commit
		if (! (@mysqli_commit($connection) ))
			dbshowerror($connection, "die");
		//***************** end of input data
	}



	//
	// load FIRST rankings
	$query="select teamnum from teambot where event_id = '{$sys_event_id}' "
	    . " and teamnum not in (select teamnum from alliance_unavailable where event_id = '{$sys_event_id}')"
	    . " and teamnum not in (select teamnum from alliance_team where event_id = '{$sys_event_id}') "
	    . " order by isnull(f_ranking), f_ranking ASC";
    if (debug()) print "<br>DEBUG-finalselect,f_ranking: " . $query . "<br>\n";
    if (!($result = @ mysqli_query ($connection, $query)))
		dbshowerror($connection, die);
	while($row = mysqli_fetch_array($result))
	  $top_ranked_available[] = $row['teamnum'];
    // zero f_rankcnt
    $f_rankcnt=0;
    // end of seek rankings

	//
	// loads rank values from a specifed type of ranking, allows the user to edit and resort the rankings
	//  overall, pos1, pos2, pos3

	//
	// load rank values
	//

	unset( $teamsrank);  // unsetting rank array

	if ($sort) $orderby = " order by isnull(rank_{$sort}), rank_{$sort} ";
	$query = "select teambot.teamnum teamnum, name, nickname, rank_overall, rating_overall,
		rating_overall_off, rating_overall_def, rank_pos1, rating_pos1, rank_pos2, rating_pos2,
		rank_pos3, rating_pos3
		from teambot, team where teambot.event_id = '{$sys_event_id}' and teambot.teamnum=team.teamnum
		  and teambot.teamnum not in (select teamnum from alliance_unavailable where event_id = '{$sys_event_id}') {$orderby}";

	// query and load
	if (! ($result = @ mysqli_query ($connection, $query)))
		dbshowerror($connection, "die");

	// load teams
	while($row = mysqli_fetch_array($result))
	{
		$teamnum=$row["teamnum"];

		// load team array
		$team[$teamnum]=$row;

		// load sort array
		$teamsrank[$teamnum]=$row["rank_" . $sort];

	}

	if($error_message!="")
		print "<br><br><font color=\"red\"><b>{$error_message}</font></b>\n";

    // seqential entry display
	if($edit==10)//this might change if all other data fields are filled in
	{
		print "<form method=\"POST\" action=\"/finalselect.php?edit=10\">\n";

		// submit button
		print "<br><INPUT TYPE=\"Submit\" name=\"Submit\" VALUE=\"Submit\" ALIGN=middle BORDER=0>\n";

		print "<br><table>\n<tr valign = \"top\"><td><table>\n";

		//print Alliance numbers:
		print "<tr><td><table border=1>\n";
		for($i=1; $i<=8; $i++)
			print "<tr><td>Alliance {$i}</td></tr>\n";
		print "</table></td>\n";

		$declared_next = 1;
		$last_data = array();
			//stores team numbers for the last column because you work backwards
			//-1 if input, -1 if no data

		for($num = 1; $num <=3; $num++)
		{
			print "<td><table border=1>\n";

			for($tnQ=1; $tnQ<=8; $tnQ++)
			{
				$tn=$tnQ;
				if($num==3)
				{
					$tn = 9-$tn;
					//print "{$tn}, ";
				}

				if(!($result = @ mysqli_query ($connection, "select * from alliance_team
					where event_id = '{$sys_event_id}' and alliancenum = {$tn} and position = {$num}") ))
					dbshowerror($connection, "die");

				$found=0;
				while($row = mysqli_fetch_array($result))
				{
					if($found==0)
					{
						if($num==3)
							$last_data[$tn]=$row["teamnum"];
						else
						{
							$un=0;
							if(!($result2 = @ mysqli_query ($connection, "select * from alliance_unavailable
								where event_id = '{$sys_event_id}' and alliancenum = {$row["teamnum"]}") ))
								dbshowerror($connection, "die");
							while($row2 = mysqli_fetch_array($result2))
								$un=1;
							if(un==0)
								print "<tr><td>{$row["teamnum"]}</td></tr>\n";
							else
								print "<tr><td><b>{$row["teamnum"]}</b></td></tr>\n";
						}
					}
					$found = 1;
				}
				if($found == 0)
				{
					if($declared_next==1)
					{
						$declared_next=2;

						if($num==3)
							$last_data[$tn]=-1;
						else
							print "<tr><td><input type=\"text\" name=next size=4 maxlength=4></td>\n";
					}//next place
					else
					{
						if($num==3)
							$last_data[$tn]=-2;
						else
							print "<tr><td>-</td>\n";
					}
				}
			}//end of for loop to 8 to count teams

			if($num==3)
			{
				for($tn=1; $tn<=8; $tn++)
				{
				  if($last_data[$tn]==-2)
					print "<tr><td>-</td>\n";
				  else if($last_data[$tn]==-1)
					print "<tr><td><input type=\"text\" name=next size=4 maxlength=4></td>\n";
				  else
				    print "<tr><td>$last_data[$tn]</td>";

				// display submit button
				// JLV work on later -- need to redo entire table struction and query order for this to work
			    // print "<td>";
				// print "<INPUT TYPE=\"submit\" name=\"Submit\" VALUE=\"Submit\" ALIGN=middle BORDER=0>\n";
			    // print "</td>\n";

				}
			}

            print "<tr>\n";

			print "</table></td>\n";
		}//end of for counter to 3 for each column
		print "</tr></table></td>";

		//** print refused list:
		print "<td>&nbsp;&nbsp;</td><td><table border=1><tr><td>Refused:</td></tr>";
		if(!($result = @ mysqli_query ($connection, "select teamnum from alliance_unavailable
			where event_id = '{$sys_event_id}' and refused=true") ))
			dbshowerror($connection, "die");
		while($row = mysqli_fetch_array($result))
			print "<tr><td>{$row["teamnum"]}</td></td>\n";
		print "</table>\n</table>\n";
		//** end of printing refused list

		print "<br>Refusal: <input type=\"text\" name=refused size=4 maxlength=4><br><br>\n";

	} //big one, only runs if in edit mode 10

//****************************************************

  	//display all the alliances

	if($edit != 10)
	{
		if($edit == 11)
			print "<form method=\"POST\" action=\"/finalselect.php?edit=12\">\n";
		else if($edit == 13)
			print "<form method=\"POST\" action=\"/finalselect.php?edit=14\">\n";
		else print "<form method=\"POST\" action=\"/finalselect.php?edit=1\">\n";

        // submit button
		print "<br> <INPUT TYPE=\"submit\" name=\"Submit\" VALUE=\"Submit\" ALIGN=middle BORDER=0>\n";

		print "<br><table>\n<tr valign = \"top\"><td><table border=1>\n";


		for($i=1; $i<=8; $i++)
		{
			print "<tr><td>Alliance {$i}</td>\n";

			if($edit!=11 && $edit!=13)
			{
				for($num=1; $num<=3; $num++)
				{
					if (! ($result2 = @ mysqli_query ($connection, "select teamnum from alliance_team
						where event_id = '{$sys_event_id}' and alliancenum = '{$i}' and position = '{$num}'") ))
						dbshowerror($connection, "die");
					while($row2 = mysqli_fetch_array($result2))
						print "<td>".teamhref($row2[0]).$row2[0]."</a></td>";
				}
			}

			if($edit == 11)
			{
			    // zero out f_rankcnt
			    //$rankcnt=0;

				if (! ($result = @ mysqli_query ($connection, "select teamnum from alliance_team
					where event_id = '{$sys_event_id}' and alliancenum = '{$i}' and position = '1'") ))
					dbshowerror($connection, "die");
				$row = mysqli_fetch_array($result);

				if($row)
					print "<td><input type=\"text\" name=\"team{$i}\" size=4 maxlength=4 value=\"{$row["teamnum"]}\"><td>\n";
				else
					print "<td><input type=\"text\" name=\"team{$i}\" size=4 maxlength=4 value=\"{$top_ranked_available[$f_rankcnt++]}\"<td>\n";
			}
			if($edit == 13)
			{
				for($q=1; $q<=3; $q++)
				{
					if (! ($result2 = @ mysqli_query ($connection, "select teamnum from alliance_team
						where event_id = '{$sys_event_id}' and alliancenum = '{$i}' and position='{$q}'") ))
						dbshowerror($connection, "die");
					$row2 = mysqli_fetch_array($result2);

					if(isset($row2["teamnum"]))
					{
						$tmp = $row2["teamnum"];
						print "<td><input type=\"text\" name=\"team{$i}{$q}\" size=4 maxlength=4
							value=\"$tmp\"><td>\n";
					}
					else
						print "<td><input type=\"text\" name=\"team{$i}{$q}\" size=4 maxlength=4><td>\n";
				}

			}

			print "</tr>\n";
		}

		// close table and close formatting column
		print "</table>\n</td>\n";

		//** print refused list:
		print "<td>&nbsp;&nbsp;</td><td>\n<table border=1>\n<tr><td>Refused:</td></tr>\n";
		if(!($result = @ mysqli_query ($connection, "select teamnum from alliance_unavailable
			where event_id = '{$sys_event_id}' and refused=true") ))
			dbshowerror($connection, "die");
		while($row = mysqli_fetch_array($result))
			print "<tr><td>{$row["teamnum"]}</td></td>\n";
		print "</table></table><br>\n";
		//** end of printing refused list
		//print "</table><br>\n";

		// enter refused
		// JLV fix entry
		// print "<br>Refusal: <input type=\"text\" name=refused size=4 maxlength=4><br><br>\n";

	}


    // show next seeded alliance
	print "Next highest seed: {$top_ranked_available[$f_rankcnt]}<br><br>\n";


    //
    // messaging to the field
    //

	if($_POST["message"])
		$message=mysqli_real_escape_string($connection, $_POST["message"]);
	else
	{
		if (! ($result = @ mysqli_query ($connection, "select message from message where facility = 'finals_selection'" ) ))
			dbshowerror($connection, "die");
		$row = mysqli_fetch_array($result);
		$message = $row["message"];
	}

	if(isset($message) && $message!="" && $edit)
	{
		if (! ($result = @ mysqli_query ($connection, "update message set message='{$message}' where facility = 'finals_selection'" ) ))
			dbshowerror($connection, "die");
	}
	//message to field:
	$message = stripcslashes ($message);

	if($edit)
	{
		print "Message to field: \n<input type=\"text\" name=\"message\" size=100 maxlength=200 value=\"{$message}\"><br>\n";

        // submit button
		print "<INPUT TYPE=\"submit\" name=\"Submit\" VALUE=\"Submit\" ALIGN=middle BORDER=0><br>\n";

        // end form
        print "</form>\n";

	}
	else
		print "Message to field: {$message}<br>";


  	//End of Conrads work
//***************************************************

	// Return navigation
	print "<br><a href=\"{$base}\">Return to Home</a>\n";

	// close the form if in edit mode
	//***  if ($edit) print "\n</form>\n";


	$editT=$edit;//use $editT as the edit for a link
	if($editT==11 || $editT==12)
		$editT=15;

	//
	// rankings table
	//
	// set up table heading
	print "
	<!-- Rankings table --->
	<hr>
	<!--- table for display data --->
	<table valign=\"top\">
	<tr>
	<th><a href=\"{$url_root}finalselect.php?edit={$editT}&sort=overall\">Overall Rank</a>";  // end of print
	print "</th>";

	// positions rank and rating
	if ($field_positions)  // if using field positions
	{
	  // loop through positions
	  for($i=1; $i<4; $i++)
	  {
		  // pos
		  print "<th><a href=\"{$url_root}finalselect.php?edit={$editT}&sort=pos{$i}\">Position {$i} Rank</a>";
		  print "</th>\n";
	  }
	}

	// end heaing row
	print "</th></tr>\n";


	// loop through each entry
	foreach ($teamsrank as $teamnum=>$rank)
	{
		// set edit field values
		$editfield = "<input type=\"text\" name=\"{$teamnum}_rank_{$sort}\" size=4 maxlength=4 value=\"{$team[$teamnum]["rank_{$sort}"]}\">";

		// display values
		print "<tr>\n";

		// print team num, name
		print "<td><a href=\"/teaminfo.php?teamnum={$teamnum}\">{$teamnum} - ";
		print substr($team[$teamnum]["name"], 0, $team_name_display_max-6);
		// if nickname, print too
		if ($team[$teamnum]["nickname"]) print " " . substr($team[$teamnum]["nickname"], 0, 12);
		print "</a></td>\n";

		// overall rank
		print "\n<td align=\"center\">";
		print $team[$teamnum]["rank_overall"];
		print "</td>\n";


		// positions rank and rating
		if ($field_positions)  // if using field positions
		{
			// loop through positions
			for($i=1; $i<4; $i++)
			{
				// rank
				print "\n<td align=\"center\">";
				print $team[$teamnum]["rank_pos" . $i];
				print "</td>";
			}
		}

		// close row
		print "</tr>\n";
	}

	// close table
	print "</table>";


	pfooter();
?>