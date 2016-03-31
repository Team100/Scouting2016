<?php
//
// $Revision: 3.0 $
// $Date: 2016/03/14 22:56:41 $
//
// bluealliance.inc
//
// Functions and field mappings to be used for The Blue Alliance API
//
// There are three broad areas in this file:
//  1. custom field mapping and functions to map The Blue Alliance data
//      into the compsystem database
//  2. Update functions which perform retreival and updating of data,
//      usually called by a web page or background process
//  3. Library functions used by update and other functions that simplify
//      writing an update function.
//

// inlucde httpful library
include ('lib/httpful.phar');


// ***********************************************************************************
//
// Custom field mapping section
//
// Functions and variables in this section set up maps from Blue Alliance data 
//  to compsystem database columns
//
// - These maps may change and Blue Alliance data structures change
// - Some of the maps change each year for the game
// - Some yearly-changing maps are defined in params.inc instead of here
//    because they also involve UI configuration.  Try to reference that map in 
//    comments below, then direct people to the params.inc file for configuration.
// - In general, the map definitions should provide configuration of the data loads
//    without additional programming.
//


// ***************************
//
// The Blue Alliance API fieldmaps
//
// Sets up map from Blue Alliance to our scheme structure
//
// Format of map:
//   $tba_fieldmap - fieldmap which is an array of key value pairs 
//        where key is the Blue Alliance field name and 
//        value is the Compeition System column name.
//
//    Variable name is $tba_[BlueAllianceEntity]_to_[CompSystemTableName]
// 

// tBA event to event table
$tba_event_to_event = array ("key"=>"event_id","short_name"=>"name","name"=>"long_name","event_code"=>"event_code",
   "event_type_string"=>"event_type","event_type"=>"event_type_id","year"=>"year","location"=>"location",
   "website"=>"website" );
   
// tBA team to team table
$tba_team_to_team = array ("team_number" =>"teamnum","nickname"=>"name","location"=>"location",
   "website"=>"website","name"=>"sponsors","rookie_year"=>"rookie_year");
   
// tBA history

// tBA team to teambot table
$tba_team_to_teambot = array ("botname"=>"name");

// tBA match to match_instance table
$tba_match_to_match_instance = array ("match_number"=>"tba_match_num", "time"=>"scheduled_utime");

// tBA match to match_instance_alliance table
$tba_match_to_match_alliance = array ("score"=>"score");

// tBA match to match_team table
$tba_match_to_match_team = array ();

// tBA stats to teambot
$tba_stats_to_teambot = array ("oprs"=>"opr","dprs"=>"dpr","ccwms"=>"ccwm");


// tBA stats to match_instance_alliance
$map_ranking_to_teambot = array (0=>"f_ranking", 1=>"teamnum");




// ***************************
//
// The Blue Alliance API functions -- Custom Mapping Functions
//
// The first functions are defined as part of custom-mapping the Blue Alliance
//  data to competition system database.  These functions are essentially custom.
//

      
//
// Specific field to field mapper functions 
//
// basic pattern
// Field Mapper: [Element] to {Element]
// function: tba_map_[element]($tba_response_array,$tba_dbarray)
//   $tba_responsearray - segment of tba response that can be interpretted 
//        as an array in a foreach
//   $tba_array - tBA data, formatted as an array key value 
//        pairs where the key is the db column name and the value
//        is the data. (optional parameter)
//
//  Maps [element field] 
//     from blue alliance returned object in array to a tba_dbarray.
//  Returns the tba_dbarray


// Field Mapper: team key to teamnum
// function: tba_map_teamnum($tba_response_array,$tba_dbarray)
//   $tba_responsearray - segment of tba response that can be interpretted 
//        as an array in a foreach
//   $tba_array - tBA data, formatted as an array key value 
//        pairs where the key is the db column name and the value
//        is the data. (optional parameter)
//
//  Returns the tba_dbarray
//
//  Maps team key to teamnum
//     from blue alliance returned object in array to a tba_dbarray.

function tba_map_teamnum($tba_response_array,$tba_dbarray)
{
  sscanf($tba_response_array->key, "frc%d", $teamnum);

  // place into dbarray
  $tba_dbarray["teamnum"]=$teamnum;
  
  return($tba_dbarray);
}


//
// Special-purpose extraction function 
// function: tba_getmatcharray($tba_response_array)
//   $tba_responsearray - segment of tba response that can be interpretted 
//        as an array in a foreach
//   
//   Returns: match array with event, match type, and matchnum
//
//  Note: the array can be passed as a starting or template $tba_dbarray
//

function tba_getmatcharray ($tba_response_array)
{
  // initialize vars
  $offset=0;
    
  // set type based on comp level
  switch ($tba_response_array->comp_level)
  {
    case "qm": 
      $matcharray[type] = 'Q';
      break;
    case "ef":
      $matcharray[type] = 'F';
      $matcharray[final_type] = 'E';
      break;    
    case "qf":
      $offset=24;
      $matcharray[final_type] = 'Q';
      $matcharray[type] = 'F';
      break;
    case "sf":
      $offset=36;
      $matcharray[final_type] = 'S';      
      $matcharray[type] = 'F';
      break;
    case "f":
      $offset=42;
      $matcharray[final_type] = 'F';
      $matcharray[type] = 'F';
      break;
    default:
      $matcharray[type] = 'P';
   }   
   
  // set match number by type, if finals, need to calculate number
  //  calculation is based on set * match number
  if ($matcharray[type] == 'F')
    $matcharray[matchnum]=$offset + $tba_response_array->set_number * 3 + $tba_response_array->match_number;
  else
    $matcharray[matchnum]=$tba_response_array->match_number;
   
   return ($matcharray);
}


// ***********************************************************************************
//
// Data Retrieval and Update Functions
//
// These functions are usually called from a web page or from a background daemon
//  periodically processing updates
//
// Note: these functions directly print errors or progress messages rather than
//  returning up the stack.
//


// function: tba_get_event_teams()
//   Returns - TRUE if successful, FALSE if otherwise
//
// Gets event teams based on $sys_event_id

function tba_get_event_teams()
{
  global $sys_event_id;
  global $tba_AppId;
  global $tba_error;
  global $connection;
  global $tba_team_to_team, $tba_team_to_teambot;

  // get data
  if (! ($tba_response = tba_getdata("http://www.thebluealliance.com/api/v2/event/{$sys_event_id}/teams")))
  {
    print "API returned " . $tba_error['message'] . "<br>\n";
    return(FALSE);
  }
  else
  {
    // inform user
    print "<br>Updated team ";

    foreach($tba_response->body as $key=>$teamobj)
    {
      // get teamnum
      $tba_dbarray = tba_map_teamnum($teamobj, "");
      // map fields from response for team table
      $tba_dbarray = tba_mapfields($tba_team_to_team, $teamobj, $tba_dbarray);

      // update event data in event table
      tba_updatedb("team", array ("teamnum"=>$tba_dbarray["teamnum"]), $tba_dbarray);

      // get teamnum and reset db array
      $tba_dbarray = tba_map_teamnum($teamobj, "");
      $tba_dbarray['event_id']=$sys_event_id;

      // map fields from response for teambot  table
      $tba_dbarray = tba_mapfields($tba_team_to_teambot, $teamobj, $tba_dbarray);

      // update event data in event table
      tba_updatedb("teambot", array ("event_id"=>$sys_event_id, "teamnum"=>$tba_dbarray["teamnum"]), $tba_dbarray);

      // inform user
      print "{$tba_dbarray["teamnum"]}, ";

      }

    // commit
    if (! (@mysqli_commit($connection) ))
      dbshowerror($connection, "die");

    // Inform user
    print "<br>&nbsp;&nbsp;&nbsp; ... team loading complete.<br>\n";
    
    return(TRUE);

  } // end of else from REST query

  return(TRUE);
  
} // end of function


// function: tba_get_match_data()
//   Returns - message string of what to print as a final message
//
// Gets match data for event based on $sys_event_id

function tba_get_matches()
{
  global $sys_event_id;
  global $tba_AppId;
  global $tba_error;
  global $connection;
  global $tba_match_to_match_instance, $tba_match_to_match_alliance, $tba_match_to_match_team;
  
  // get data
  if (! ($tba_response = tba_getdata("http://www.thebluealliance.com/api/v2/event/{$sys_event_id}/matches")))
    print "API returned " . $tba_error['message'] . "<br>\n";
  else
  {

/*
      // get data
    try
    {
      $tba_url = "http://www.thebluealliance.com/api/v2/event/{$sys_event_id}/matches";
      $tba_response = \Httpful\Request::get($tba_url)
         ->addHeader('X-TBA-App-Id',$tba_AppId)
         ->send();
    } catch (Exception $e)
    {
    print "Exception";
       showerror("Caught exception from Blue Alliance: " . $e->getMessage());
       return;
    }
*/

    foreach($tba_response->body as $key=>$matchobj)
    {
      // get match array from match
      $matcharray = tba_getmatcharray($matchobj);
       // create match ID array and use and starting dbarray
      $tba_dbarray = array_merge ( array ("event_id"=>$sys_event_id, ), $matcharray);
      $match_id_array = array ("event_id"=>$sys_event_id, "type"=>$matcharray['type'],
                          "matchnum"=>$matcharray['matchnum']);
      // map fields from response for match_instance table
      // $tba_dbarray = tba_mapfields($tba_match_to_match_instance, $matchobj, $tba_dbarray);
      // update event data in event table
      tba_updatedb("match_instance", $match_id_array, $tba_dbarray);

      // update match alliance table
      // iterate match alliances
      foreach($matchobj->alliances as $colorkey=>$allianceobj)
      {
        if ($colorkey == "blue") $color='B'; else $color='R';
        
        // set match alliance array
        $match_alliance_array = array_merge ($match_id_array,array("color"=>$color));
        $tba_dbarray = $match_alliance_array;
        
        // map fields from response for match_instance table
        $tba_dbarray = tba_mapfields($tba_match_to_match_alliance, $allianceobj, $tba_dbarray);

        // update event data in event table
        tba_updatedb("match_instance_alliance", $match_alliance_array, $tba_dbarray);
        
        // loop through each team in alliance and update match_team table
        foreach($allianceobj->teams as $teamkey)
        {
          // turn teamkey to teamnum
          sscanf($teamkey, "frc%d", $teamnum);

          // set match key array
          //   Note: it doesn't use color in the kay or the alliance key;
          //     however, color must be inserted in the table (avoids a crazy join)
          $match_team_array = array_merge ($match_id_array, array ("teamnum"=>$teamnum));
          $tba_dbarray = $match_team_array;
          
          // map fields from response for match_instance table
          // $tba_dbarray = tba_mapfields($tba_match_to_match_team, $match_team_array, $tba_dbarray);

          // manually add color
          $tba_dbarray = array_merge ($tba_dbarray, array("color"=>$color));

          // update event data in event table
          tba_updatedb("match_team", $match_team_array, $tba_dbarray);
          }
        } // end of alliance

      // inform user
      print "{$tba_dbarray["type"]}{$tba_dbarray["matchnum"]}, ";

    } // end of match

    // commit
    if (! (@mysqli_commit($connection) ))
      dbshowerror($connection, "die");

    // Inform user
    print "<br>&nbsp;&nbsp;&nbsp; ... match loading complete.<br>\n";
    return(TRUE);

  } // end of else from REST query

  return(TRUE);

} // end of function


// function: tba_get_event_stats()
//   Returns - message string of what to print as a final message
//
// Gets event stats based on $sys_event_id

function tba_get_event_stats()
{
  global $sys_event_id;
  global $tba_AppId;
  global $tba_error;
  global $connection;
  global $tba_stats_to_teambot;

  // get data
  //                                  http://www.thebluealliance.com/api/v2/event/{$sys_event_id}/stats
  if (! ($tba_response = tba_getdata("http://www.thebluealliance.com/api/v2/event/{$sys_event_id}/stats")))
    print "API returned " . $tba_error['message'] . "<br>\n";
  else
  {
    // loop through each type of stat in map function
    foreach($tba_stats_to_teambot as $tag=>$column)
      foreach($tba_response->body->$tag as $teamnum=>$value)
      {
        $id_array = array("event_id"=>$sys_event_id, "teamnum"=>$teamnum);
        tba_updatedb("teambot", $id_array, array($column=>$value));
        print $teamnum . ", ";
      }

    // commit
    if (! (@mysqli_commit($connection) ))
      dbshowerror($connection, "die");

    // Inform user
    print "<br>&nbsp;&nbsp;&nbsp; ... stats loading complete.<br>\n";

    return(TRUE);

  } // end of else from REST query

  return(TRUE);
  
} // end of function


// function: tba_get_event_rankings()
//   Returns - message string of what to print as a final message
//
// Gets event rankings based on $sys_event_id
// Decode the event rankings based on display variable structures

function tba_get_event_rankings()
{
  global $tba_AppId;
  global $tba_error;
  global $connection;
  {

    return(TRUE);

  } // end of else from REST query

  return(TRUE);
  
} // end of function



// function: tba_get_team_history($teamnum)
//   Returns - message string of what to print as a final message
//
// Gets event teams based on $sys_event_id

function tba_get_team_history($teamnum)
{
  global $sys_event_id;
  global $tba_AppId;
  global $tba_error;
  global $connection;
  {
  
    return(TRUE);

  } // end of else from REST query

  return(TRUE);
  
} // end of function

// function: tba_get_team_awards($teamnum)
//   Returns - message string of what to print as a final message
//
// Gets event teams based on $sys_event_id

function tba_get_team_awards($teamnum)
{
  global $sys_event_id;
  global $tba_AppId;
  global $tba_error;
  global $connection;
  {
  
    return(TRUE);

  } // end of else from REST query

  return(TRUE);
  
} // end of function



// ***********************************************************************************
//
// The Blue Alliance API functions -- Standard functions / Library section
//
//

// 
// $tba_error array
//   This array is populated when a function needs to return more descriptive 
//   data.  The array includes the following keyed elements:
//    - state - boolean that is true if in error or advisory condition 
//    - fatal - boolean if function things functions above should stop processing
//    - code - the error code being returned by function
//    - message - a message to display to user
//    - response - (object or array) functional details of entire response
//       (example: the http response object
//
// Example array def:
//  array('state'=>FALSE, 'fatal'=>FALSE, 'code'=>, 'message'=>"", 'response'=>"");
//


//  
// function: tba_getdata($tba_url) - 
//   $tba_url - URL to call
//
// Returns:
//   $tba_response if success
//   0 if call created an error
//   NOTE: 
//
// looks up last modified in database and sends with data.
// Calls $tBA_url and returns the data.  
// Prepares global $tba_error array if in error state.  See $tba_error doc
//  for structure of the error array

function tba_getdata($tba_url)
{
  global $tba_AppId;
  global $tba_error;
  $tba_error = array("state"=>FALSE);
  
  
  // test - change with real functions
  // $last = "Tue, 29 Mar 2016 14:46:03 GMT";
  //$last = "";
  
  try
  {
    $tba_response = \Httpful\Request::get($tba_url)
       ->addHeader('X-TBA-App-Id',$tba_AppId)
       ->addHeader('If-Modified-Since',$last)
       ->send();
  } catch (Exception $e)
  {
    showerror("Caught exception from Blue Alliance: " . $e->getMessage());
        return(0);
  }

  // process return code
  // get first letter of code and switch off
  $code = $tba_response->code;
  $code1 = substr($code, 0, 1);
  
  // debug
  if (debug()) print_r($tba_response);
  
  switch ($code1)
  {
    case "4":
      $tba_error = array('state'=>FALSE, 'fatal'=>TRUE, 'code'=>$code, 
         'message'=>"received error {$code} from server. " . $tba_response->body->Error,
         'response'=>$tba_response);
      return(FALSE);
      break;
    
    case "3":
      $tba_error = array('state'=>FALSE, 'fatal'=>FALSE, 'code'=>$code, 
         'message'=>"your data is up-to-date. No update needed.",
         'response'=>$tba_response);
      return(FALSE);
      break;
      
    default:
      return($tba_response);
      break;
  }
  if ($code1 != "2")
    return(FALSE);


  // tba_setlastmod($tba_url, $response->headers['last-modified']);

  
  return($tba_response);
}



//
// function: tba_getlastmod($tba_url)
//   $tbaurl - URL that returned last modified time
//
// searchs database for last modified time returned from tBA for URL 
//  and returns the string.
//

//
// function: tba_setlastmod($tba_url, $lastmod)
//   $tbaurl - URL that returned last modified time
//   $lastmod - string with last modified time 
//
// stores last modified string for URL
// returns nothing.
//



// ****
//
// tBA field general mapping and database functions
//
// The functions below bridge from the Blue Alliance API objects and fields
//  to the system database tables and columns.
//
// Basic use pattern:
//
//  // map tBA fields into db array
//  $tba_dbarray = tba_mapfields($tba_event_to_event, $tba_response->body, $tba_dbarray (or "") );
//  // use dbarray to update event table
//  tba_updatedb("event", array ("event_id"=>$new_sys_event_id, "year"=>2016), $tba_dbarray);
//  commit the query
//


//
// function: tba_mapfields($tba_fieldmap,$tba_response_array,$tba_dbarray)
//   $tba_fieldmap - fieldmap which is an array of key value pairs 
//        where key is the Blue Alliance field name and 
//        value is the Compeition System column name.
//   $tba_responsearray - segment of tba response that can be interpretted 
//        as an array in a foreach
//   $tba_array - tBA data, formatted as an array key value 
//        pairs where the key is the db column name and the value
//        is the data. (optional parameter)
//
//  Maps blue alliance returned object in array to a tba_dbarray.
//  Returns the tba_dbarray

function tba_mapfields ($tba_fieldmap, $tba_responsearray, $tba_dbarray)
{
   // loop through fieldmap picking up any named fields from object
   // and storing them in dbarray
   foreach($tba_fieldmap as $field => $value)
     if ($tba_responsearray->$field) 
       $tba_dbarray[$value] = $tba_responsearray->$field;

   return($tba_dbarray);
}


//
// function: tba_updatedb($tablename, $table_identifiers, $tba_dbarray)
//   $tablename - table name in compsys for which array applies
//   $table_identifies - formatted array of key/value pairs where the 
//        key is the db column name that is part of the identifier
//        (and thus the where clause), and the value is the data.
//   $tba_data_array - tBA data, formatted as an array of key/value 
//        pairs where the key is the db column name and the value
//        is the data.
//
// Updates database with data
//   Queries for row with data.  If found, updates.  If not found, inserts.
//

function tba_updatedb($tablename, $table_identifiers, $tba_dbarray)
{
   global $connection;
   
   // set up dummy column for test
   $dummycol="";
   foreach($table_identifiers as $column=>$value)
     if ($dummycol == "") $dummycol = $column;

   // set where clauase
   $where = fields_insert("where", $table_identifiers, "");
   
   // select to see if update or insert
   $query = "select {$dummycol} from {$tablename} where " . $where;
   if (debug()) print "<br>DEBUG:tba_updatedb, Select query: " . $query . "<br>\n";
   
   if (! ($result = @ mysqli_query ($connection, $query)))
  	  dbshowerror($connection, "die");
  	  
   // if return, update, otherwise insert
   if ($row = mysqli_fetch_array($result))
   {
     $query = "update {$tablename} set " . fields_insert("update", $tba_dbarray, "")
            .  " where " . $where;
     if (debug()) print "<br>DEBUG:tba_updatedb, insert :" . $query . "<br>\n";
     if (! (@mysqli_query ($connection, $query) ))
       dbshowerror($connection, "die");
   } 
   else
   {
     $query = "insert into {$tablename} (" . fields_insert("nameonly", $tba_dbarray, "")
             . ") values (" . fields_insert("insert", $tba_dbarray, "") . " )";
             
     if (debug()) print "<br>DEBUG:tba_updatedb, update: " . $query . "<br>\n"; 
     if (! (@mysqli_query ($connection, $query) ))
       dbshowerror($connection, "die");
   }
   
   return;
}




//
// function: tba() - 
//   $
//
//


//
// function: tba() - 
//   $
//
//



//
// function: tba() - 
//   $
//
//





//
// ****************************************************************************
//
// end of bluealliance.inc -- do not leave any lines under the end of this closure
?>