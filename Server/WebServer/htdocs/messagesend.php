<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - Team Details page
  //
  require "page.inc";

  // get variables
  $edit=$_GET["edit"];

  // header and setup
  pheader("Send a Message to Field");
  $connection = dbsetup();


  // define lock array, fields arrays
  $dblock = array(table=>"message",where=>" facility = 'finals'");


  // handle update if returning from edit mode
  if ($edit == 2)
  {
  	// load operation
  	if ( $_POST[op] == "Save" )
	{
		// check db
		//dblock($dblock,"check");

		// load form fields
		$message = $_POST["message"];
		$query = "update message set message = '{$message}' where facility = 'finals_selection';";

		// process query
		if (! (@mysqli_query ($connection, $query) ))
			dbshowerror($connection, "die");

		// commit
		if (! (@mysqli_commit($connection) ))
			dbshowerror($connection, "die");
	}

	// abondon lock
	dblock($dblock,"abandon");

    // update completed
    $edit = 1;

   }


	// lock tables if in edit mode
	if ($edit) dblock($dblock,"lock");  // lock row with current user id


  // create page
  //

  // get message
  if (!($result = @ mysqli_query ($connection, "select message from message where facility = 'finals_selection'")))
    dbshowerror($connection);

  // get row
  $row = mysqli_fetch_array($result);
  $message = $row["message"];

  // field options
  $options["tr"] = 1;  // add tr tags

  //if ($edit)
  {
    // if in edit mode, signal save with edit=2
  	print "<br><form method=\"POST\" action=\"/messagesend.php?edit=2\">\n";
  }

  // input box


  print "
  <input type=\"text\" name=\"message\" size=100 maxlength=200 value=\"{$message}\">
  <br>
  <br>
  <INPUT TYPE=\"submit\" name=\"op\" VALUE=\"Save\" ALIGN=middle BORDER=0>
  <INPUT TYPE=\"submit\" name=\"op\" VALUE=\"Cancel\" ALIGN=middle BORDER=0>
  </form>
  ";   // end of form


  // add edit link or submit button
  // print dblockshowedit($edit, $dblock, "/teamdetails.php?teamnum={$teamnum}") . "\n";

  // close the form if in edit mode
  if ($edit) print "\n</form>\n";

  print "</tr>\n</table>\n";


  // return and home buttons
  print "<br><a href=\"{$base}\">Return to Home</a>\n";

  //if ($admin) print "&nbsp;&nbsp;&nbsp; <a href=\"/admin.php\">Sys Admin</a>\n";
  print "<br>\n";


   pfooter();
  ?>
