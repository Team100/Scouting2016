<?php
//
// $Revision: 3.0 $
// $Date: 2016/03/14 22:56:41 $
//
// params.inc
//
// Contains all user and admin settable parameters needed by the competition system
//
// Notes:
//  - General params are in first section
//  
//  - 

// Timezone setting
//  -- the string must come from a PHP list found here:
//       http://php.net/manual/en/timezones.php
$sys_time_zone='America/Los_Angeles';

//
// Host team - Team that uses this db
$host_teamnum=100;
$host_teamnum=4;
$host_team_name="Wildhats";
// don't change next line
$frcteam=sprintf("frc%'04d",$host_teamnum);

//
// The Blue Alliance API id
//   Format is FRC team number:user:version.  Change as needed.
$tbaAppId= $frcteam . ':compsystem:v02';


// Site parameters
//
// site names as in http://site/
$site = "localhost";
// base - everything after site.  Example: /compsys.  Leave at "/" if nothing.
$base = "/";

// 
// Field positions
//
// Set to TRUE if field positions (where a robot starts and plays) matter in the analysis.  Some years very important, 
//   some years not important at all.  Set to false to skip and field positions will not be used in analysis forms
//
//$field_positions = TRUE;
$field_positions = FALSE;

//
// Field parameters
//
// Notes:
//  Field parameters are intended to be changed every year for a given game
// 
//  display - what is displayed in fields prompts to user
//  inputlen - length of input box
//  maxlen - max length of entry. Browser will not accept more characters.
//  default_value - value placed in input box as a default.  Can be null if no value is expected.
//
//
//   Should we create a "alliance position" switch too?  (for later)

// Comment out unsed params or set used to FALSE

//
// Play Fields -- analyzed from field play, pit, or other analysis of robot
//

$dispfields["Play"][0] = array("used"=>TRUE, "display"=>"Oper Status", "inputlen"=>4, "maxlen"=>4, "default_value"=>"");
$dispfields["Play"][1] = array("used"=>TRUE, "display"=>"Drivetrain", "inputlen"=>10, "maxlen"=>20, "default_value"=>"");
$dispfields["Play"][2] = array("used"=>TRUE, "display"=>"Shooter?", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
$dispfields["Play"][3] = array("used"=>TRUE, "display"=>"High Shooter?", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
$dispfields["Play"][4] = array("used"=>TRUE, "display"=>"Pickup?", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
$dispfields["Play"][5] = array("used"=>TRUE, "display"=>"Scaling Mech?", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
$dispfields["Play"][6] = array("used"=>TRUE, "display"=>"Special Mech?", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
$dispfields["Play"][7] = array("used"=>TRUE, "display"=>"Portcullis", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][8] = array("used"=>TRUE, "display"=>"Cheval-Door", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][9] = array("used"=>TRUE, "display"=>"Moat", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][10] = array("used"=>TRUE, "display"=>"Ramparts", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][11] = array("used"=>TRUE, "display"=>"Drawbridge", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][12] = array("used"=>TRUE, "display"=>"Sally Port", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][13] = array("used"=>TRUE, "display"=>"Rock Wall", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][14] = array("used"=>TRUE, "display"=>"Rough Terrain", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][15] = array("used"=>TRUE, "display"=>"Low Bar", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][16] = array("used"=>TRUE, "display"=>"Speed Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"5");
$dispfields["Play"][17] = array("used"=>TRUE, "display"=>"Pickup Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"5");
$dispfields["Play"][18] = array("used"=>TRUE, "display"=>"Low Shoot Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"5");
$dispfields["Play"][19] = array("used"=>TRUE, "display"=>"High Shoot Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"5");
$dispfields["Play"][20] = array("used"=>TRUE, "display"=>"Breach Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"5");
// $dispfields["Play"][21] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][22] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][23] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][24] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][25] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][26] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][27] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][28] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][29] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][30] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][31] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][32] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][33] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][34] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][35] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");


//
// Match Fields -- analyzed in each match.  Data specific to that match
//

$dispfields["Match"][0] = array("used"=>TRUE, "display"=>"Portcullis", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
$dispfields["Match"][1] = array("used"=>TRUE, "display"=>"Cheval-Door", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Match"][2] = array("used"=>TRUE, "display"=>"Moat", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Match"][3] = array("used"=>TRUE, "display"=>"Ramparts", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Match"][4] = array("used"=>TRUE, "display"=>"Drawbridge", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Match"][5] = array("used"=>TRUE, "display"=>"Sally Port", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Match"][6] = array("used"=>TRUE, "display"=>"Rock Wall", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Match"][7] = array("used"=>TRUE, "display"=>"Rough Terrain", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Match"][8] = array("used"=>TRUE, "display"=>"Low Bar", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Match"][9] = array("used"=>TRUE, "display"=>"Speed Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"2");
$dispfields["Match"][10] = array("used"=>TRUE, "display"=>"Pickup Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"2");
$dispfields["Match"][11] = array("used"=>TRUE, "display"=>"Low Shoot Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"2");
$dispfields["Match"][12] = array("used"=>TRUE, "display"=>"High Shoot Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"2");
$dispfields["Match"][13] = array("used"=>TRUE, "display"=>"Breach Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"2");
// $dispfields["Match"][5] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Match"][6] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Match"][7] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Match"][8] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Match"][9] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Match"][10] = array("used"=>TRUE, "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");




//
// function that returns the seed score for a match
// give raw scores (startine with mine), penalty points, and other
function seedscore($my_raw_score, $their_raw_score, $my_penalties, $their_penalties, $my_other, $their_other)
{
   $my_score=$my_raw_score-$mypenalties;
   $their_score=$their_raw_score-$their_penalties;

   if($my_score>$their_score)
      $seed=$my_score+2*($their_score)+5;//winner's seed
   else if($my_score<$their_score)
      $seed=$their_raw_score;//looser's seed
   else
      $seed=3*$my_score;//tie
   return($seed);
}


//
// Tablet Server File Locations
//

$tablet_server_home = "D:/jlv/tmp/tablet";
$tablet_templates = $tablet_server_home . "/templates";
$tablet_ingest = $tablet_server_home . "/ingest";
$tablet_ingest_complete = $tablet_server_home . "/ingest-complete";
$tablet_teamimages = $tablet_server_home . "/team_images";

//
// Database parameters
//

$dbname = "competition";

$dbuser = "compuser";

$dbpass = "100hats";

$dbhost = "localhost";


//
// Schedule files and documentation
//
$schedule_xls = "doc/qualification_schedule.xls";
$schedule_csv = "doc/qualification_schedule.csv";


//
// Message  timing for message recv application
//

$message_refresh = 8;


// ***************************
//
// Security
//
//

// default user password (filled in)
$default_password = "wild4woodside";

// administrator users
$admins = array("jlv", "mckay");

// location of security files and routines

//$htpasswdfile = "C:\Program Files\Apache Software Foundation\Apache2.2\admin\htpasswd.master";
$htpasswdfile = "..\admin\htpasswd.master";
$htpasswdexe = "..\bin\htpasswd.exe";
// $htpasswdexe = "C:\apps\Apache24\bin\htpasswd.exe";


// ***************************
//
// Misc App Configuration
//

// background colors used for red and blue teams
$background_colors = array ("R"=>"#ffaaaa", "Red"=>"#ffaaaa", "B"=>"#aaaaff", "Blue"=>"#aaaaff");

// background colors for other areas
$lyellow = '#ffff66';
$lgreen = '#ccffcc';
$lblue = '#ccccff';
$lred = '#ffcccc';

//length of the updated by field
$maxlengthchangeby=200;


// ***************************
//
// The Blue Alliance API mapping fields
//
// Field mappings to the API are defined in bluealliance.inc for performance reasons
//

//
// end of params.inc -- do not leave any lines under the end of this closure or html is not well-formed with top header line
//
?>