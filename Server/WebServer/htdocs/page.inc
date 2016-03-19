<?php
//
// $Revision: 3.0 $
// $Date: 2016/03/14 22:56:41 $
//
// page.inc
//
// Functions to be used on every page.  Documentation shown above function (and available in compilation).
//

require "params.inc";


// software release version
$release_version="3.0";


//
// set time zone
//
date_default_timezone_set($sys_time_zone);


//
// function: debug
//  returns true if $debug is true, otherwise returns false
$debug=TRUE;
//$debug=FALSE;
function debug()
{
    global $debug;

    if ( $debug === TRUE ) return(TRUE); else return(FALSE);
}

//
// ****************************************************************************
//
// Database Functions
//
// 


// 
// function: dbshowerror($connection, $die) - show database error
//  $connection = mysql connection context
//  $die = if text flag set to "die", process terminates with this error 
//
//  Displays database error using showerror as the display mechanism.

function dbshowerror($connection, $die)
{
    if ( $die == "die")
 	die(showerror("Fatal Database Error " . mysqli_errno($connection) . ", " . mysqli_error($connection)));
    else
      showerror("Non-fatal Database Error " . mysqli_errno($connection) . ", " . mysqli_error($connection));
}

//
// function: dbsetup()  - database start
//  Returns: $connection MySQL handle
//
//  Sets up single connection for database interaction.  $connection is returned.
//  Also loads sys_event_id, as it is needed everywhere in system
//

function dbsetup()
{
  // take variable values from the global space
  global $dbhost, $dbuser, $dbname, $dbpass;
  global $sys_event_id, $sys_event_name;
  
  // get connection
  if(!($connection = @ mysqli_connect($dbhost,$dbuser,$dbpass, $dbname)))
     die("Database Error:" . mysqli_connect_errno() . " : " . mysqli_connect_error());
     
  // turn autocommit off
  mysqli_autocommit($connection, FALSE);

  // load default event_id and name for this instance from database
  $query="select value, name from system_value, event where skey = 'sys_event_id' and event_id = value";
  if (! ($result = @mysqli_query ($connection, $query) ))
  	dbshowerror($connection, "die");
  $row = mysqli_fetch_array($result);
  $sys_event_id = $row["value"];
  $sys_event_name = $row["name"];
  
  if ($sys_event_id == "") $sys_event_name = "No competition event selected !!";

  return($connection);
}


//
// function: dblock($dblock, $op) - controls locks on rows: locks, checks for lock or abandons lock
//   $dblock array can be configured two ways:
//      1) An associative array that consists of two values:
//   	    table - table name in which to place lock
//          where - where clause to identify correct row(s)
//      2) An array of associative arrays that consist of the two values above.  In this case
//           the routine performs dblock function for each table/where clause in the array
//    $op - operation: "lock", "check", "abandon"
//      "lock" - lock the row with the current $user
//      "check" - check that the $user still holds a lock 
//      "abandon" - abandon or release the lock
//	"changedby" - update field 'updatedby' in the given table
//
// Implements poor-man's row-locking.  This single function performs all operations, via the $op directive.
//  Function looks for locked column and if locked column exists, 
//
//  Can also be used with a "dblockshowedit" function, which will query for a lock and either show a "steal" page
//   or display Save and Cancel buttons.
//
// Note: Can't seem to find a mysqli describe function to test if the column is there
//
//
function dblock($dblock, $op)
{
    global $connection; // database connection
    global $user;      	// user id
    global $_GET;

    // if dblock var not set, show error
    if (! isset($dblock))
    {
    	showerror("DB lock function called with no DB Lock info.");
    	return;
    }

    // determine if we are called as a single table and convert to the multitable array
    if (isset($dblock["table"]))
    {
    	$working[0]["table"] = $dblock["table"];
    	$working[0]["where"] = $dblock["where"];
    }
    else
    	// assume an array instead
    	$working = $dblock;

    
    
    // loop through array locking each table/row
    foreach($working as $wlock)
    {
    	// look for row-locked user  
	if (! ($result = @mysqli_query ($connection, "select locked from {$wlock["table"]} where {$wlock["where"]}") ))
		dbshowerror($connection, "die");

	// get row and field
	$row = mysqli_fetch_array($result);
	$rowlocked = $row["locked"];

	// perform various operations	    	
    	switch ($op)
    	{
    	   case "lock":
	    	// if row locked and not stealing, abort
	    	if ($rowlocked != "")
	    	   if ($_GET["dblocksteal"] != "1")
			  die(showerror("Database row in {$wlock["table"]} is being edited by {$rowlocked}. You may steal the edit or wait (hit BACK and refresh)."));

	    	// add userid as locked user  
	    	if (! (@mysqli_query ($connection, "update {$wlock["table"]} set locked = '{$user}' where {$wlock["where"]}") ))
			dbshowerror($connection, "die");
		
	 	break;
	 	
	    case "check":
	    	// if row locked by another user, abort transaction
	    	if ($rowlocked != $user)
	    	   if ($rowlocked == "")
			die(showerror("Database row in {$wlock["table"]} as edited by someone else.  Please abort and return to Home Page!"));
	    	   else 
			die(showerror("Database row in {$wlock["table"]} has been locked by a different user - {$rowlocked}. Edit aborted.  Hit back to continue."));

	    	// add userid as locked user  
	    	if (! (@mysqli_query ($connection, "update {$wlock["table"]} set locked = NULL where {$wlock["where"]}") ))
			dbshowerror($connection, "die");	    
		
		break;

	    case "abandon":
	    	// if row locked by this user, free row
	    	if ($rowlocked == $user)
		    	if (! (@mysqli_query ($connection, "update {$wlock["table"]} set locked = NULL where {$wlock["where"]}") ))
				dbshowerror($connection, "die");	
		break;
	    case "changedby":
	    	if ($rowlocked == $user)
	    	{
	    		global $maxlengthchangeby;
	    		
	    		if (! ($result = @mysqli_query ($connection, "select updatedby from {$wlock["table"]} where {$wlock["where"]}") ))
				dbshowerror($connection, "die");
			$row = mysqli_fetch_array($result);
			$updatedby = $row["updatedby"];
			
			$date = date('d m/d h:i');
			$updatedby = "{$user} {$date}- {$updatedby}";
			$updatedby = substr ($updatedby, 0, $maxlengthchangeby);
				
		    	if (! (@mysqli_query ($connection, "update {$wlock["table"]} set updatedby = '{$updatedby}' where {$wlock["where"]}") ))
				dbshowerror($connection, "die");
		}
	    	break;
	 } // end of switch   
      } // end of foreach

	// commit
	if (! (@mysqli_commit($connection) ))
		dbshowerror($connection, "die");
	
}




//
// function: dblockshowedit($edit, $dblock, $urlroot)
//   $edit - flag is non-zero if editing
//   $dblock array can be configured two ways:
//      1) An associative array that consists of two values:
//   	    table - table name in which to place lock
//          where - where clause to identify correct row(s)
//      2) An array of associative arrays that consist of the two values above.  In this case
//           the routine performs dblock function for each table/where clause in the array
//   $urlroot - urlroot to connect to edit link (or override link)
//
//  Returns: string to display to user
//
// If in edit mode, show edit options.  If not in edit mode, check for lock and 
//   show editability to user
//
function dblockshowedit( $edit, $dblock, $urlroot )
{
    global $connection; // database connection
    global $user;      	// user id

    // if dblock var not set, show error
    if (! isset($dblock))
    {
    	showerror("DBshowedit function called with no DB Lock info.");
    	return;
    }
    
    // determine if we are called as a single table and convert to the multitable array
    if (isset($dblock["table"]))
    {
    	$working[0]["table"] = $dblock["table"];
    	$working[0]["where"] = $dblock["where"];
    }
    else
    	// assume an array instead
    	$working = $dblock;



    // if in edit mode, show button, otherwise check what type of edit to show
    if ($edit)
    {
	$string = "<INPUT TYPE=\"submit\" name=\"op\" VALUE=\"Save\" ALIGN=middle BORDER=0>\n"
	. "&nbsp;<INPUT TYPE=\"submit\" name=\"op\" VALUE=\"Cancel\" ALIGN=middle BORDER=0>\n";
    }
    else
    {
    	// loop through array locking each table/row
    	foreach($working as $wlock)
    	{
		// move through tables identifying any locking
		
		// look for row-locked user  
		if (! ($result = @mysqli_query ($connection, "select locked from {$wlock["table"]} where {$wlock["where"]}") ))
			dbshowerror($connection, "die");

		// get row and field
		$row = mysqli_fetch_array($result);
		$rowlocked = $row["locked"];
		
		// if not null, add to list
		if ($rowlocked != "") $showlocked[] = array("table"=>$wlock["table"], "user"=>$row["locked"]);
	}

     	// if list exists, see how many elements and how user
     	if (isset ($showlocked))
     	{
     		// how many elements
		if (count($showlocked) > 1)
  		// multiple tables
		{
			$string = "Locked: ";
			foreach($showlocked as $locker)
				$string = $string . "{$locker["table"]} by {$locker["user"]},";
		
			$string = $string . " <a href=\"{$urlroot}&edit=1&dblocksteal=1\">!Steal page!</a>\n";
		}
		else
			$string = "Locked by {$rowlocked}. <a href=\"{$urlroot}&edit=1&dblocksteal=1\">!Steal page!</a>\n";
     	
     	}
     	else
     		$string = "<a href=\"{$urlroot}&edit=1\">Edit this page</a>\n";

    } // end of if edit else
    return ($string);
}



//
// function: showupdatedby($dblock)
// 
// if the user is an admin return a string of hwo has updated the table
// 	defined by the dblock paramaters (look above)
// $dblock is an array with keys of "table" and "where" that define what table is being accessed
//

function showupdatedby($dblock)
{
	global $admins;
	global $user;
	global $admin;
	global $connection;
	global $sys_event_id;
	
	if($admin)
	{
		// if dblock var not set, show error
		if (! isset($dblock))
		{
			showerror("DBshowedit function called with no DB Lock info.");
			return;
		}

		// determine if we are called as a single table and convert to the multitable array
		if (isset($dblock["table"]))
		{
			$working[0]["table"] = $dblock["table"];
			$working[0]["where"] = $dblock["where"];
		}
		else
		// assume an array instead
			$working = $dblock;
			
		foreach($working as $wlock)
		{    	
			$query="select updatedby from {$wlock["table"]} where {$wlock["where"]}";
			if (! ($result = @mysqli_query ($connection, $query) ))
				dbshowerror($connection, "die");
			$row = mysqli_fetch_array($result);
			print "<br>{$wlock["table"]} Updated by: {$row["updatedby"]}";
		}
	}
}



//
// ****************************************************************************
//
// Page display functions
//

// 
// function: showerror($text)
//  $text - text to show as error message
//
//  Displays error in red to the user.  Instructs user to use BACK button, and provides a link to the home page.
// 

function showerror ($text)
{
  global $base;
  print "<h2><font color=\"red\">{$text}</font></h2>\nPlease use BACK button to correct and resubmit.\n"
  . "<br><br><a href=\"{$base}\">Return to Home Page</a><br><br>";
}

//
// function: pheader
//  $title - title to appear in html title
//  $titleonly = text flag that makes the title only visible in the title bar when set to "titleonly"
//      May be DEPRICATED
//  $options
//    - array of options
//    - openhead - leave head open  
//
//  Intended to be called from every page.  Sets up $user, starts HTML document.
//
//   set up HTML header with title and header properties
//   set H2 title in page as header info
//

function pheader($title, $titleonly, $options)
{
  global $user;
  global $admins;
  global $admin;
  global $event_id;
  
  // get user if exists, then set admin privledge
  if (isset($_SERVER["PHP_AUTH_USER"]))
  {
  	$user = $_SERVER["PHP_AUTH_USER"];
  	
  	// check for admin rights
  	$index = array_search($user, $admins);
  	if (! ($index === FALSE)) $admin=TRUE; else $admin=FALSE;
  	
  }

  // format page header
  print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
                      \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
  print "\n<HTML>\n<HEAD><TITLE>{$title}</TITLE>\n";
  
  // if not "openhead"
  if (! (isset ($options["openhead"]))) print "</HEAD>\n<BODY>\n";
  
  // if not titleonly
  if (! (isset ($titleonly)) || (! ($titleonly == "titleonly" )))
  	print "<h2>{$title}</h2>";
  		
}


//
// function: pfooter()
//
//   set up HTML footer, close db connection and commit trasactions
//

function pfooter()
{
  global $user;		// user id
  global $admin;	// admin rights
  global $connection;   // database connection
  global $host_team_name;  	// team host name
  global $sys_event_name;       // event name
  global $site;         // site in site URL
  global $base;         // base in site URL
  
  print "<br><br><hr>" 
    . "<a href=\"{$base}\">Competition System Home</a><br>";
    
  // show user if exists
  if ($user)
  {
  	print "Logged in as {$user} ";
  	if ($admin) print " -- administrator";
  	print "\n&nbsp;&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp  \n";
        print "<a href=\"http://logout@{$site}{$base}/\">Logout</a>";
  	print "<br>\n";
  }
  
  print "<i>{$host_team_name} -- {$sys_event_name}</i>\n</BODY>\n</HTML>";
  
  // if database connection, close down
  if ($connection)
  {
  	// commit any transactions
	  if (! (@mysqli_commit ($connection) ))
		dbshowerror($connection, "die");
	  // close database connection
	  mysqli_close($connection);
  }
}


//
// function: teamhref($teamnum)
//  $teamnum - number of team
//  
// Returns: href text to teaminfo page
//
// convenience function to return team <a href BLAH> on to teaminfo page.  
//  Does not supply the closing </a>.
// 
//

function teamhref ( $teamnum)
{
  return ("<a href=\"/teaminfo.php?teamnum={$teamnum}\">");
}

//
// function: tabtextfield($edit, $options, $data, $fieldname, $fieldtag, $size, $maxlenth, $value)
//  $edit - edit flag, 1 if editing
//  $options - options to add (see below)
//  $data - dataarray with associative element named fieldname
//  $fieldname - name of field (in associative element, in forms, etc.
//  $fieldtag - tag to display by field.  if "DisplayField", display field translation is referenced
//  $size - size of editing text box
//  $maxlength - max length of input allowed
//  $defvalue - value to place in text box if in editing mode.  Otherwise this value comes from $data
//       ---- Not implemented yet
//
// Displays for reading or for edit in two <td> boxes.  Intended as a generic method to layout boxes
//  within layout tables.
//
// Options in options array:
//  ["tr"] = TRUE - add beginning and ending <tr> tags
//  ["notag="] = TRUE - do not print field label/tag.  Just the field itself
//  ["notd"] = TRUE - do not print <td> open and close
//  ["href"] = TRUE - enclose data value in href tag
//   
//

function tabtextfield( $edit, $options, $data, $fieldname, $fieldtag, $size, $maxlength, $value )
{
  // 
  // set up options for inclusion / exclusion
    // tr
    if ($options["tr"]) {$beg_tr="<tr valign=\"top\">\n"; $end_tr="</tr>"; }
    //
    // no td -- TRUE means don't include <td> tags
    if (! ($options["notd"] )) {$beg_td="<td>"; $end_td="</td>"; }
    //
    // notag -- don't include tag
    if (! ($options["notag"])) $tag = "{$beg_td}{$fieldtag}{$end_td}";
    
    // look for value
    if (! ($value)) $value = $data[$fieldname];

    // set up href option
    // href
    if ($options["href"])
    	{
    	  // strip off http start if there is one  
    	  if ( ! ($web = (strstr($value, 'http://'))) === FALSE)
	  	$beg_href = "<a href=\"http://" . substr($web, 7) . "\">";    
	  else
        	$beg_href = "<a href=\"http://{$value}\">" ; 
	
	  $end_href = "</a>";
	}
   
    
    // set match length
    if (! $maxlength) $maxlength = $size;
    
  // set up edit and non-edit mode  
  if ($edit)
    $ret="{$beg_tr}{$tag}{$beg_td}<input type=\"text\" name=\"{$fieldname}\" size={$size} maxlength={$maxlength} value=\"{$value}\">{$end_td}{$end_tr}";
  else  
    $ret="{$beg_tr}{$tag}{$beg_td}{$beg_href}{$value}{$end_href}{$end_td}\n{$end_tr}\n";

   return($ret);
}




//
// function: tabtextarea ($edit, $options, $data, $fieldname, $fieldtag, $row, $col, $value )
//  $edit - edit flag, 1 if editing
//  $options - options to add (see below)
//  $data - dataarray with associative element named fieldname
//  $fieldname - name of field (in associative element, in forms, etc.
//  $fieldtag - tag to display by field.  if "DisplayField", display field translation is referenced
//  $row - rows of text box when editing
//  $col - cols of text box when editing
//  $defvalue - value to place in text box if in editing mode.  Otherwise this value comes from $data
//       ---- Not implemented yet
//
// Similar to tabtextfield, lays out display or edit box
//
// Options in options array:
//  ["tr"] = TRUE - add beginning and ending <tr> tags
//  ["notd"] = TRUE - don't use <td> tags to seperate values
//  ["notag="] = TRUE - do not print field label/tag.  Just the field itself
//   
//

function tabtextarea( $edit, $options, $data, $fieldname, $fieldtag, $row, $col, $value )
{
  //
  // set up options for inclusion / exclusion
    // tr
    if ($options["tr"]) {$beg_tr="<tr valign=\"top\">\n"; $end_tr="</tr>"; }
    //
    // no td -- TRUE means don't include <td> tags
    if (! ($options["notd"] )) {$beg_td="<td>"; $end_td="</td>"; }
    //
    // notag -- don't include tag
    if (! ($options["notag"])) $tag = "<tr><td><u>{$fieldtag}</u></td></tr>\n";
    
    // look for value
    if (! ($value)) $value = $data[$fieldname];
    
    // set match length
    if (! $maxlength) $maxlength = $size;
  
  // start table
  $ret="{$beg_tr}{$beg_td}<table>{$tag}<tr><td>";
      
  // set up edit and non-edit mode  
  if ($edit)
    $ret=$ret . "<textarea name=\"{$fieldname}\" rows=\"{$row}\" cols=\"{$col}\">{$value}</textarea>";
  else  
    $ret=$ret . "{$value}\n";
        
  // finish table
  $ret=$ret . "</td></tr></table>{$end_td}{$end_tr}\n";
  
  return($ret);
}





//
// function: texttfield ($fieldtag, $fieldname, $size, $maxlength, $value )
//   $fieldtag - user-displayed tag in front of field
//   $fieldname - name used for variables and db field
//   $size - size of edit text box
//   $maxlength - maximum length of input allowed
//   $value - value placed in field
//

function texttfield( $fieldtag, $fieldname, $size, $maxlength, $value )
{
  if (! $maxlength) $maxlength = $size;
  // check for value existance and transform into string
  if ( $value ) $value = "value=\"{$value}\"";
  
  print "<tr><td>{$fieldtag}</td><td><input type=\"text\" name=\"{$fieldname}\" size={$size} maxlength={$maxlength} {$value}></td></tr>\n";
}

//
// fucntion: hiddenfield ( $fieldname, $value )
//   $fieldname - name used for variables and db field
//   $value - value placed in field
//
//  Used to place a hidden field within a fox.
//

function hiddenfield( $fieldname, $value )
{
  // check for value existance and transform into string
  if ( $value ) $value = "value=\"{$value}\"";
  
  print "\n<input type=\"hidden\" name=\"{$fieldname}\" {$value}>\n";
}


//
// alliancefield ($color)
//   $color - (Red / Blue) designating color of alliance
//
//  paints 3 text boxes for fields
//  
//

function alliancefield ( $color )
{

  while ( $teamcnt++ < 3 )
    print "\n<input type=\"text\" name=\"team_{$color}_{$teamcnt}\" size=4 maxlength=4>\n";
}



//
// ****************************************************************************
//
// fields functions
//
//


//
// function: fields_load: ($post, $fieldnames)
//  loads fields names from post operation
//   $post = text value for get or post
//   $fieldnames = array of field names assumed to match html form names
//
// Loads fieldnames from $GET or $POST.  If $post is set to "post", uses a $post variable.
//

function fields_load ( $post, $fieldnames )
{
   // iterate through field names
   foreach ($fieldnames as $fieldname)
    {
      // assign name a value
      if ( $post == "post" )
	$formfields[$fieldname] = $_POST[$fieldname];
      else
        $formfields[$fieldname] = $_GET[$fieldname];
    }
    
    return ($formfields);
}



//
// function: ( $operation, $formfields, $fieldnames )
//  fields_insert: create insert string
//  $operation - name, value, where
//        nameonly or fieldname - name of fields, comma separated
//        insert - values used in SQL insert statement
//        where - where-clause name=value, and separate
//        update - update clause
//  $formfields - array created in fields_load of each field 
//      (an ordered associative array of name=>value for each field)
//  $fieldnames - optional array of field names to be inserted 
//      (an ordered array of db column fieldnames)
//
//   Returns string for insertion into SQL statement.
//
//  Creates strings for insertion into SQL statements.  Strings include column names (fieldname or nameonly),
//   insert values ('value','value'...), or update/where statements (field='value')
//
//

function fields_insert ( $operation, $formfields, $fieldnames )
{
   $insert="";
   
   // if field names, use the array that is passed, otherwise iterate over formfields
   if ($fieldnames)
     foreach($fieldnames as $fieldname)
	$insert = fields_insert_add ( $operation, $formfields, $fieldname, $insert);
   else
     // iterate through fields and create insert string
     foreach ($formfields as $fieldname => $value)
	$insert = fields_insert_add ( $operation, $formfields, $fieldname, $insert);
   
   return ($insert);
}

 // function used in fields_value
function fields_insert_add ( $operation, $formfields, $fieldname, $insert)
  {
  	// get db connection
  	global $connection;
  	      	
      	switch ($operation)
      	{
      	  case "nameonly":
      	  case "fieldname":
      	    	// add comma
      	        if ( $insert ) $insert = $insert . ", ";
      	
      	  	$insert = $insert . $fieldname;
      	  	break;
      	  	
      	  case "insert":
      	  	// clean sql inputs before submitting -- not sure why this is commented out -- jlv
      	  	// $formfields[$fieldname] = mysqli_real_escape_string($connection, $formfields[$fieldname]);
      		// clean sql inputs before submitting
      	  	// $formfields[$fieldname] = mysqli_real_escape_string($connection, $formfields[$fieldname]);
      	  	
      	    	// add comma
      	        if ( $insert ) $insert = $insert . ", ";
      	  	
      		// add insert value
      		if ( $formfields[$fieldname] == "" ) 
        		$insert = $insert . "NULL";
      		else
      		{
      			// clean sql inputs before submitting
      			$formfields[$fieldname] = mysqli_real_escape_string($connection, $formfields[$fieldname]);
      			
        		$insert = $insert . "'$formfields[$fieldname]'";
        	}
        	break;
        	
          case "update":
      		// clean sql inputs before submitting
      	  	$formfields[$fieldname] = mysqli_real_escape_string($connection, $formfields[$fieldname]);

      	    	// add comma
      	        if ( $insert ) $insert = $insert . ", ";
      	        
      	  	// add insert value
      		if ( $formfields[$fieldname] == "" ) 
        		$insert = $insert . "$fieldname = NULL";
      		else
        		$insert = $insert . "$fieldname = '$formfields[$fieldname]'"; 
        	break;
        	
          case "where":
                // clean sql inputs before submitting
      	  	$formfields[$fieldname] = mysqli_real_escape_string($connection, $formfields[$fieldname]);

      	    	// add comma
      	        if ( $insert ) $insert = $insert . " and ";
      	        
      	  	// add where value
      		if ( $formfields[$fieldname] == "" ) 
        		$insert = $insert . "$fieldname = NULL";
      		else
        		$insert = $insert . "$fieldname = '$formfields[$fieldname]'"; 
        	break;
      	  	
          default:
          	showerror("Code error: No operation value assigned in fields_insert_add.");
	}

    return ($insert);
}


//
//
// ****************************************************************************
//
// parameters handling functions
//
//    functions that use and manipulate the configurable variables set up 
//     in params.inc
//
//

// 
// function: param_array ($paramgroup)
//  creates array of needed form fields
//   $paramgroup - group name for paramters (Play, Pit, Match, etc.)
//   Retuns an array of valid fieldnames
//

function param_array ($paramgroup)
{
   global $dispfields;

   $cnt=0;
   $fcnt=0;
   
   // loop while var is set.  Don't go over 50 as a safety check
   while ( isset($dispfields[$paramgroup][$cnt]) && ( $cnt < 50 ) )
   {
   	// if used is set TRUE, include in group
   	if ($dispfields[$paramgroup][$cnt]["used"]===TRUE)
		$fieldnames[$fcnt] = $paramgroup . "Field_" . $fcnt++;
	
	// increment field counter
   	$cnt = ++$cnt;
   }
   
   return($fieldnames);
}

//
//
// function: tabparamfields($edit, $options, $data, $paramgroup);
//   $edit - edit option
//   $options - identical to tabtextfield options (passed to tabtextfield)
//      Addtional options for this function:
//         pagebreak = if non-zero (set), break page for these number of columns (2 or more)
//         pagebreakstring = string inserted to cause pagebreak.  Works
//           in tandem with pagebreak
//   $data - data row with field params (similar to tabtextfield)
//   $paramgroup - group name for paramters (Play, Pit, Match, etc.)
//
//   Displays param fields in a table form, similar to texttabfield.  Displays all param fields
//    in a paramgroup
//

function tabparamfields($edit, $options, $data, $paramgroup)
{
   global $dispfields;
   
 //
 // calculate pagebreak rows if needed
 //
 //  Note: pagebreak rows may not be the same and $cnt because used==FALSE rows shouldn't be counted
 //
 if ( isset ($options["pagebreak"]))		// if set
 {
	// set flag
	$pagebreakrow=1;
     	if ($options["pagebreak"])			// if non-zero
	{
	   // loop through to get total
	   $cnt=0;
	   $rowtot=0;
	   while ( isset($dispfields[$paramgroup][$cnt]) && ( $cnt < 50 ) )
	   {
		if ($dispfields[$paramgroup][$cnt]["used"]===TRUE)
			$rowtot = ++$rowtot;
		$cnt = ++$cnt;
	   }
	   
	   // calculate pagebreak row
	   $pagebreakrow = ceil ($rowtot / $options["pagebreak"]);
	}
  }
  else $pagebreakrow=0;
	
   // loop while var is set.  Don't go over 50 as a safety check
   $cnt=0;
   $rowcnt=1;
   while ( isset($dispfields[$paramgroup][$cnt]) && ( $cnt < 50 ) )
   {
   	// if param is used place in group
   	if ($dispfields[$paramgroup][$cnt]["used"]===TRUE)
   	{
		$ret = $ret . "\n" . tabtextfield ( $edit, $options, $data, $paramgroup . "Field_" . $cnt, 
			$dispfields[$paramgroup][$cnt]["display"],
			$dispfields[$paramgroup][$cnt]["inputlen"], $dispfields[$paramgroup][$cnt]["maxlen"],
			$dispfields[$paramgroup][$cnt]["defaultvalue"]);
		
		 // pagebreak if needed
		 if ($pagebreakrow)				// if pagebreaking
		     if (! ($rowcnt++  % $pagebreakrow  ))			// if zero
			   $ret = $ret . $options["pagebreakstring"];
	}
	$cnt = ++$cnt;


   }
   
   return($ret);
      
}

//
//
// function: tabparamfields($operation, $formfields, $paramgroup)
//  display/edit parameterized fields
//
//  NOTE: operands similar to fields_insert
//  $operation - name, value, where
//        nameonly or fieldname - name of fields, comma separated
//        insert - values used in SQL insert statement
//        where - where-clear name=value, comma separated
//  $formfields - array created in fields_load of each field 
//      (an ordered associative array of name=>value for each field)
//  $paramgroup - group name for paramters (Play, Pit, Match, etc.)
//
//   Adds SQL statement fields to SQL statements, similar to fields_insert.
//   Creates strings for insertion into SQL statements.  Strings include column names (fieldname or nameonly),
//   insert values ('value','value'...), or update/where statements (field='value')
//

function param_insert( $operation, $formfields, $paramgroup)
{
   global $dispfields;

   $cnt=0;
   
   // loop while var is set.  Don't go over 50 as a safety check
   while ( isset($dispfields[$paramgroup][$cnt]) && ( $cnt < 50 ) )
   {
   	if ($dispfields[$paramgroup][$cnt]["used"]===TRUE)
		$fieldnames[$cnt] = $paramgroup . "Field_" . $cnt;
	$cnt = ++$cnt;
   }
   
   // call field insert   
   return(fields_insert ( $operation, $formfields, $fieldnames ));
      
}



//
// function: param_load ($post, $paramgroup)
//  loads paramterized fields names from post operation
//   $post = text value for get or post
//   $paramgroup - group name for paramters (Play, Pit, Match, etc.)
//
//  Loads parameter fields from $GET or $POST vars.
//
function param_load( $post, $paramgroup)
{
   global $dispfields;

   $cnt=0;
   
   // loop while var is set.  Don't go over 50 as a safety check
   while ( isset($dispfields[$paramgroup][$cnt]) && ( $cnt < 50 ) )
   {
	if ($dispfields[$paramgroup][$cnt]["used"]===TRUE)
		$fieldnames[$cnt] = $paramgroup . "Field_" . $cnt;
	$cnt = ++$cnt;
   }
   
   // call field insert   
   return(fields_load ( $post, $fieldnames ));      
}

//
// function:param_report ($data, $paramgroup, $public))
//   $data - array or field=>value row arrays (multiple teams)
//   $paramgroup - group name for paramters (Play, Pit, Match, etc.)
//   $public - if set to 1 then only print competitive info
//   $teamcnt - count of total teams in paramter report
//
//   Returns a string of table rows
//
// Builds team comparison rows
// 
//

function param_report ($data, $paramgroup, $public, $teamcnt)
{
   global $dispfields;

   $cnt=0;
   
   // loop while var is set.  Don't go over 50 as a safety check
   while ( isset($dispfields[$paramgroup][$cnt]) && ($dispfields[$paramgroup][$cnt]["used"]===TRUE) && ( $cnt < 50 ) )
   {
	if ($dispfields[$paramgroup][$cnt]["used"]===TRUE)
	{
		// find field name
		$fieldname = $paramgroup . "Field_" . $cnt; 

		// start row
		$ret = $ret . "<tr><td>{$dispfields[$paramgroup][$cnt]["display"]}</td>\n";

		// loop through each team array
		// if public, only print first 3, not competition
		if ($public) $tot=3; else $tot=$teamcnt;
		for($i=0; $i<$tot; $i++)
			$ret = $ret . "<td>{$data[$i][$fieldname]}</td>";

		 //end row
		$ret = $ret . "</tr>\n";
	}

	// bump counter
	$cnt = ++$cnt;
   }
   
   return ($ret);
}




//
// ****************************************************************************
//
// teams functions
//

//
// function: alliances_load ($post)
//  $post = value or get or post
// assumes team_color_cnt format on variable -- loads into teams arrays
//

function alliances_load( $post )
{
   // iterate through colors and team numbers
   foreach (array("Red", "Blue") as $color)
    {
      $teamcnt=0;
      while ($teamcnt++ < 3)
       // assign name a value
       if ( $post == "post" )
  	 $teams[$color][$teamcnt] = $_POST["team_{$color}_{$teamcnt}"];
       else
         $teams[$color][$teamcnt] = $_GET["team_{$color}_{$teamcnt}"];
     }
    return ($teams);   
}

  
// 
// function: teams_validate ( $teams )
//  $teams - array of teams loaded in alliances_load
//
//  Returns: FALSE if a team doesn't exist and shows error for each team.
//
//  Validates each team in array exists in teambot table.
//

function teams_validate ( $teams )
{
   // get database connection
   global $connection;
   global $sys_event_id;
   
   // iterate through each team and check db for existance
   foreach ( $teams as $color =>$alliance )
   {
     foreach ( $alliance as $team )
     {
       // query db
       if (! ($results =  @ mysqli_query ( $connection, "select teamnum from teambot where event_id = '{$sys_event_id}' and teamnum = {$team}")))
          dbshowerror("die");
       
       // if no result, team does not exist.
       if (! ($row = @ mysqli_fetch_array($results)))
          {
            showerror("Team {$team} not found in teambot database for current event '{$sys_event_id}'");
            $error=1;
          }      
     }
    }
   if ($error) return(FALSE); else return(TRUE);
}


//
// ****************************************************************************
//
// end of page.inc -- do not leave any lines under the end of this closure
?>