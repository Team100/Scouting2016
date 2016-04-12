<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System
  //
  //
  //

  require "page.inc";

  // get variables

  pheader("Import Schedule CSV File");
  $connection = dbsetup();

 // if not administrator, display error.  Otherwise show admin section.
 if (! $admin)
   print "<h3>You must be an administrator to use this page.</h3>\n";
 else
 {

  // load debug
  if ($_GET['debug']) $debug=TRUE;

  // load match_date from post or from system
  if (isset($_POST['match_date'])) $match_date=$_POST['match_date'];
  else $match_date = get_system_value("schedule_import_date");

  // header
  print <<< EOF_EOF
<!--- Comment Section --->

<a href="/">Return to Home</a>
&nbsp;&nbsp;&nbsp; <a href="/admin.php">Sys Admin</a>
<p>

Imports a comma-separated schedule in the standard FIRST format.  Checks the format first, then populates database.
<p>
The process is somewhat manual.  Steps for this process are:
<ol>
<li>Obtain the FIRST schedule. Often it is formatted as an Excel file. In this case, save the file
    as a CVS file, then edit the headers out of the file using an editor such as Notepad.
<li>Make sure the file is well-formed including:
 <ul>
 <li>No extra lines at the end of file.
 <li>First letter in type of match should either be 'P', 'Q', or 'F'
 <li>Watch am/pm designation on scheulded time.  If day does not match system date, remove the day-of-week designation in file.
      Day of week trumps just a date.
 <li>No headers or anything but data
 <li>File must be named qualification_schedule.csv
 <li>General function 1) replace ",P" with "pm,P" to get times right, 2) may need to replace day of week with nothing
 </ul>
<li>Upload the schedule file in CSV format by browsing to the file
<li>Check validity of field mapping on the web page upload (it will be displayed).  Often the FIRST format can change.  If the field format needs
    modification, delete the upload file, edit the source file and upload it again.  work until it looks right.
<li>Import the file, which turns the uploaded file into the data within the Competition System. If the process errors out, none of the rows will
    have been loaded, so try again.
</ol>

<!--- Functions Section --->
EOF_EOF
; // end of print



  // get file name without location info
  $schedule_csv_filename = substr(strrchr($schedule_csv,'/'),1);


  // if file Delete was pressed, delete file and start again
  if ($_POST["op"] == "Delete File")
  {
  	  // delete / unlink function to remove file
  	  unlink($schedule_csv);

  	  // set system value schedule_import_date
      set_system_value("schedule_import_date", NULL);

  	  print "<h3>Upload file deleted.  Try again.</h3><br>\n";
  }

  // if file Upload was pressed, save file
  if ($_POST["op"] == "Upload")
  {
  	// check if file loaded
	if (is_uploaded_file($_FILES['toProcess']['tmp_name']))
	{
		// check file name against filename portions of $schedule_csv file set in params
		if ($_FILES['toProcess']['name'] != $schedule_csv_filename)
			print "<h3>! File name not correct. Must be of the form $schedule_csv_filename.</h3>";
		else
		{
			// move file
			move_uploaded_file($_FILES['toProcess']['tmp_name'], $schedule_csv);
			print "<br><b>File successfullly uploaded.</b><br><br>";

			// set system date
            set_system_value("schedule_import_date", $match_date);
		}

	} else
		print "<h3>! File did not upload </h3>\n";
  }


  // if file doesn't exist, upload it
  if (! (file_exists ($schedule_csv)))
  {
    // set today as default
    $today = date('Y-m-d');

  	print "<h4>File does not exist on server. Upload CSV File</h5>
    <form enctype=\"multipart/form-data\" action=\"/scheduleimport.php?debug={$debug}\" method=\"POST\">
    Date of matches:
    <input type=\"text\" name=\"match_date\" size=10 maxlength=10 value=\"{$today}\">
    <br>
    <br>
  	Upload csv file of the name $schedule_csv_filename
  	<br><br>

    <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"10240\">
    <input type=\"file\" name=\"toProcess\" size=\"50\" maxsize=\"100\">
    <br><br>
    <input type=\"submit\" name=\"op\" value=\"Upload\">
	</form>
  	"; // end of print
  }
  else
  //
  // file exists.  Show to user or process, depending on op code
  //
  {
  	// load file
  	$file = file($schedule_csv);

  	// if posted as import, perform operation and report
  	if ($_POST["op"] == "Import")
  	{
  		//
  		// run import logic:
  		//   - inserts into schedule table
  		//   - from schedule table, does SQL inserts into indidiidual tables.  Shows progress and restuls
  		//      on page as it inserts into various tables.
  		//

  		//
  		// insert each row into schedule table
  		//
  		print "<p>\n<u><b>Processing Steps for Import</b></u>\n<ul>";

		// notify of delete
		print "<li>Deleting temp table schedule roles...\n";
		$query = "delete from schedule";

		// print if in debug mode
		if ($debug) print "<br>Query:" . $query;

		if (! (@mysqli_query($connection, $query)))
  			dbshowerror($connection, "die");
  		print "done\n";

  		// notify
  		print "<li>Importing from csv file into table...\n";
  		foreach ($file as $line)
  		{
  			//
  			// clean any characteristics of the text line
  			//   NOTE:if you add it here, add it to the clean section above

  			// remove * from name
            $line = str_replace("*", "", $line);

            // end of clean string section


  			// parse fields
  			$fields = explode ( ",", rtrim ( $line));
  			if ($debug) print "\n<br><br>Field:" . var_dump ( $fields);

  			// insert into schedule table
  			// format of explode array:
  			//   Time,MatchType,MatchNum,Blue1,Blue2,Blue3,Red1,Red2,Red3


  			//
  			// clean any characteristics of the text line
  			//   NOTE:if you add it here, add it to the clean section above

  			// remove * from name
            $line = str_replace("*", "", $line);


  			// parse fields
  			$fields = explode ( ",", rtrim ( $line));
  			if ($debug) print "\n<br><br>Field:" . var_dump ( $fields);

  			//
  			// special checking on needed fields
  			//
  			//   NOTE:if you add it here, add it to the checking section below as well

  			// check date
  			//  convert to utime, then format that utime for display
  			$fields[0] = strtotime($match_date . " " . $fields[0]);

  			// check match type
  			$type = strtoupper(substr($fields[1],0,1));
  			if (! (in_array($type, ["P","Q","F"]))) $type="!!Invalid Type!!";
  			$fields[1]= $type;

  			// end of checking section


 			// not elegant but it's a one-shot -- hand-built query
 			$query = "insert into schedule
  						(scheduled_utime, type, matchnum, blue1, blue2, blue3, red1, red2, red3)
  						values ('{$fields[0]}','{$fields[1]}',{$fields[2]},{$fields[3]},{$fields[4]},{$fields[5]},{$fields[6]},
  							{$fields[7]},{$fields[8]})";

  			// process SQL
			// print if in debug mode
			if ($debug) print "<br>Query:" . $query;

  			if (! (@mysqli_query($connection, $query)))
  				dbshowerror($connection, "die");
  		}
		// nofify of completion
		print "done\n";

		//
		// inserts from schedule table into application tables
		//

		// populate match_instance
		print "<li>Adding match instance rows...";
		$query = "insert into match_instance (event_id, type, matchnum, scheduled_utime)
					select '{$sys_event_id}', type, matchnum, scheduled_utime from schedule";

		// print if in debug mode
		if ($debug) print "<br>Query:" . $query;

  		if (! (@mysqli_query($connection, $query)))
  			dbshowerror($connection, "die");
  		print "done.\n";


		// populate match_instance_detail
		//   Note: once for each color
		print "<li>Adding match instance detail rows for each alliance color...";
		$query = "insert into match_instance_alliance (event_id, type, matchnum, color)
					select '{$sys_event_id}', type, matchnum, 'B' from schedule";
  		if (! (@mysqli_query($connection, $query)))
  			dbshowerror($connection, "die");

  		// red details
		$query = "insert into match_instance_alliance (event_id, type, matchnum, color)
					select '{$sys_event_id}', type, matchnum, 'R' from schedule";

		// print if in debug mode
		if ($debug) print "<br>Query:" . $query;

  		if (! (@mysqli_query($connection, $query)))
  			dbshowerror($connection, "die");
  		print "done.\n";



		// populate match_team for each team
		//  Create iterator through colors and teams
		print "<li>Adding match instance team srows...";

		foreach (array("R","B") as $color)
		{
			// iterate each team column
			// set longcolor name
			if ($color == 'B') $longcolor = 'Blue'; else $longcolor = 'Red';

			for ($i=1; $i<4; $i++)
				{
					$query = "insert into match_team (event_id, type, matchnum, color, teamnum)
						select '{$sys_event_id}', type, matchnum, '{$color}', {$longcolor}{$i} from schedule";

					// print if in debug mode
					if ($debug) print "<br>Query:" . $query;

  					if (! (@mysqli_query($connection, $query)))
  						dbshowerror($connection, "die");
  				}
  		}
  		print "done.\n";


		//
		// commit transation
		print "<li>Commiting transactions...";

		if (! (@mysqli_commit($connection) ))
		  dbshowerror($connection, "die");

		print "done.\n";

		// end ul list, and close page
  		print "
  		</ul>
  		<p>
  		Schedule successfully imported if no errors are displayed above.
  		<p>
  		<a href=\"{$base}\">Return to Home</a>
  		<p>
  		";  // end of print
  	}
  	else
  	// show file prior to import

  	{
  		// Show sample import record
        print "Match date set: " . $match_date . "<br>";
  		print "<br><font color=\"red\"><b>The following schedule will be uploaded.  Please check accuracy of fields:</b></font><br>\n";

  		// set up table
  		//   Time,MatchType,MatchNum,Blue1,Blue2,Blue3,Red1,Red2,Red3
  		print "<table border=\"2\"><tr>\n";
  		print "<th>Time</th><th>Match Type</th><th>Match #</th><th>Blue 1</th><th>Blue 2</th>
  			<th>Blue 3</th><th>Red 1</th><th>Red 2</th><th>Red 3</th></tr>\n";

		// show each import row
  		foreach ($file as $line)
  		{
  			//
  			// clean any characteristics of the text line
  			//   NOTE:if you add it here, add it to the clean section above

  			// remove * from name
            $line = str_replace("*", "", $line);

            // end of clean string section


  			// parse fields
  			$fields = explode ( ",", rtrim ( $line));
  			if ($debug) print "\n<br><br>Field:" . var_dump ( $fields);

  			//
  			// special checking on needed fields
  			//
  			//   NOTE:if you add it here, add it to the checking section above

  			// check date
  			//  convert to utime, then format that utime for display
  			$fields[0] = date('Y-m-d h:i a', strtotime($match_date . " " . $fields[0]));

  			// check match type
  			$type = strtoupper(substr($fields[1],0,1));
  			if (! (in_array($type, ["P","Q","F"]))) $type="!!Invalid Type!!";
  			$fields[1]= $type;

  			// end of checking section

			print "<tr>";
			foreach ($fields as $field)
				print "<td>" . $field . "</td>";
			print "</tr>\n";
		}
		// close table
		print "</table>\n";

  		print "<br><b>Raw file for reference. File format:</b><br>\n";
  		print "&nbsp;&nbsp;Time,MatchType,MatchNum,Blue1,Blue2,Blue3,Red1,Red2,Red3<br>\n";
  		print "\n<pre>\n";
  		foreach($file as $line)
  			print $line;
  		print "\n</pre><br>\n";

  		// show submit
  		print "
  		<form method=\"POST\" action=\"/scheduleimport.php?debug={$debug}\">
  		<input type=\"submit\" name=\"op\" value=\"Import\">
  		&nbsp; &nbsp; &nbsp; &nbsp;<input type=\"submit\" name=\"op\" value=\"Delete File\">
  		</form>

  		<br>
  		<a href=\"{$base}\">Return to Home</a>
  		&nbsp;&nbsp;&nbsp; <a href=\"/admin.php\">Sys Admin</a>
  		";  // end of print
  	}

  }


 } // end of "if admin" qualification


   pfooter();
 ?>
