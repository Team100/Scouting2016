<?php
//
// $Revision: 3.0 $
// $Date: 2016/03/14 22:56:41 $
//
// bluealliance.inc
//
// Functions and field mappings to be used for The Blue Alliance API
//


// ***************************
//
// The Blue Alliance API fieldmap
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
$tba_event_to_event = array ("key"=>"event_id","short_name"=>"name","name"=>"long_name","event_code"=>"event_code",
   "event_type_string"=>"event_type","event_type"=>"event_type_id","year"=>"year","location"=>"location",
   "website"=>"website" );
   
   
   
   
   

// ***************************
//
// The Blue Alliance API functions
//
//


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


//
// tBA field mapping and database functions
//
// The functions below bridge from the Blue Alliance API objects and fields
//  to the system database tables and columns.
//
// Basic use pattern:
//
//  $tba_dbarray = tba_mapfields($tba_event_to_event, $tba_response->body, $tba_dbarray (or "") );
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
//   print $query . "<br>\n";
   if (! ($result = @ mysqli_query ($connection, $query)))
  	  dbshowerror($connection, "die");
  	  
   // if return, update, otherwise insert
   if ($row = mysqli_fetch_array($result))
   {
     $query = "update {$tablename} set " . fields_insert("update", $tba_dbarray, "")
            .  " where " . $where;
//   print $query . "<br>\n";
     if (! (@mysqli_query ($connection, $query) ))
       dbshowerror($connection, "die");
   } 
   else
   {
     $query = "insert into {$tablename} (" . fields_insert("nameonly", $tba_dbarray, "")
             . ") values (" . fields_insert("insert", $tba_dbarray, "") . " )";
             
//  print $query . "<br>\n"; 
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