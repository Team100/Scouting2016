<?php
  // $Revision: 2.1 $
  // $Date: 2010/04/22 04:00:55 $
  //
  // Competition System - Sample page
  //
  require "page.inc";

  // get variables
  $teamnum=$_GET["teamnum"];
  $edit=$_GET["edit"];

  // header and setup
  pheader("Team Details - " . $teamnum);
  $connection = dbsetup();

  // define lock array, fields arrays
  $dblock = array(table=>"team",where=>"teamnum = {$teamnum}");
  $table_team = array("name","nickname","org","location","students","website");

  // handle update if returning from edit mode
  if ($edit == 2)
  {
  	// load operation
  	if ( $_POST[op] == "Save" )
	{
  		// check row
  		dblock($dblock,"check");

		// load form fields
		$formfields = fields_load("post", $table_team);
		$query = "update team set " . fields_insert("update",$formfields) . " where teamnum = {$teamnum}";

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
    $edit = 0;
   }

   // lock tables if in edit mode
   if ($edit) dblock($dblock,"lock");  // lock row with current user id


print EOF_EOF
<!----- Top of page ----->
<table valign="top">
<tr valign="top">
<td>

EOF_EOF
; // end of print

  //
  // create page
  //

  // check teamnum
  if (! ($teamnum)) print "<h1>No Team Number Specified</h1>\n";

  // get team details define result set
  if (!($result = @ mysqli_query ($connection,
  	"select ". fields_insert("nameonly",NULL,$table_team) . " from team where teamnum = {$teamnum}")))
    dbshowerror($connection);

  // get row
  $row = mysqli_fetch_array($result);

  // field options
  $options["tr"] = 1;  // add tr tags

  // if in edit mode, signal save with edit=2
  if ($edit)
  	print "<form method=\"POST\" action=\"/teamdetails.php?edit=2&teamnum={$teamnum}\">\n"

  print "
  <!--- table for display data --->
  <table valign=\"top\">"
  . tabtextfield($edit,$options,$row, "name","Team name:",15,30)
  . tabtextfield($edit,$options,$row, "","",1,1)
  . "\n<tr><td><br><br></td></tr>\n<tr><td>\n";

  // add edit link or submit button
  print dblockshowedit($edit, $dblock, "/teamdetails.php?teamnum={$teamnum}") . "\n";

  // return and home buttons
  print "<br><br><a href=\"/teaminfo.php?teamnum={$teamnum}\">Return to Team Info</a><br>\n";
  print "<a href=\"/\">Return to Home</a>\n";

  // finish table and continue
  print "</td></tr></table>
  </td>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
  <td>
  <table valign=\"top\">"
  . tabtextfield($edit,$options,$row, "location","Location: ",20,40)
  . "</table>
  </td>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
  ";

  // close the form if in edit mode
  if ($edit) print "\n</form>\n";

  print "</tr>\n</table>\n";


?>

<?php
   pfooter();
  ?>
