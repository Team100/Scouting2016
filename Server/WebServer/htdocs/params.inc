<?php
//
// $Revision: 3.0 $
// $Date: 2016/03/14 22:56:41 $
//
// params.inc
//
// Contains all user and admin settable parameters needed by the competition system
//  PLEASE READ through the entire paramaters file to ascertain what is already 
//   parameter-driven.  You may find there is a parameter-based way to control the app
//   to do what you want without needing to write/modify code.
//
// Additional Notes:
// - Configuration of Blue Alliance data loading is generally configured in 
//    bluealliance.inc.  Field mappings are defined there and should generally
//    obviate the need to reprogram when fields change.
//    - There are a couple of Blue Alliance mappings that are handled in this file
//      instead of in bluealliance.inc.  These mappings change every year and involve
//      also changing UI display tags, so there are covered in a different structure
//      than the more simple tag mapping defined in bluealliance.inc.  Because they
//      change the UI, they are defined here.
// 

// Timezone setting
//  -- the string must come from a PHP list found here:
//       http://php.net/manual/en/timezones.php
$sys_time_zone='America/Los_Angeles';

//
// Host team - Team that uses this db
$host_teamnum=100;
//$host_teamnum=4;
$host_team_name="Wildhats";
// don't change next line
$frcteam=sprintf("frc%'04d",$host_teamnum);

//
// The Blue Alliance API id
//   Format is FRC team number:user:version.  Change as needed.
$tba_AppId= $frcteam . ':compsystem:v02';


// Site parameters
//
// site names as in http://site/
$site = $_SERVER['SERVER_NAME'];
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

$dispfields["Play"][0] = array("used"=>TRUE, "tag"=>"", "display"=>"Oper Status", "inputlen"=>4, "maxlen"=>4, "default_value"=>"");
$dispfields["Play"][1] = array("used"=>TRUE, "tag"=>"", "display"=>"Drivetrain", "inputlen"=>10, "maxlen"=>20, "default_value"=>"");
$dispfields["Play"][2] = array("used"=>TRUE, "tag"=>"", "display"=>"Shooter?", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
$dispfields["Play"][3] = array("used"=>TRUE, "tag"=>"", "display"=>"High Shooter?", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
$dispfields["Play"][4] = array("used"=>TRUE, "tag"=>"", "display"=>"Pickup?", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
$dispfields["Play"][5] = array("used"=>TRUE, "tag"=>"", "display"=>"Scaling Mech?", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
$dispfields["Play"][6] = array("used"=>TRUE, "tag"=>"", "display"=>"Special Mech?", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
$dispfields["Play"][7] = array("used"=>TRUE, "tag"=>"", "display"=>"Portcullis", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][8] = array("used"=>TRUE, "tag"=>"", "display"=>"Cheval-Door", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][9] = array("used"=>TRUE, "tag"=>"", "display"=>"Moat", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][10] = array("used"=>TRUE, "tag"=>"", "display"=>"Ramparts", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][11] = array("used"=>TRUE, "tag"=>"", "display"=>"Drawbridge", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][12] = array("used"=>TRUE, "tag"=>"", "display"=>"Sally Port", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][13] = array("used"=>TRUE, "tag"=>"", "display"=>"Rock Wall", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][14] = array("used"=>TRUE, "tag"=>"", "display"=>"Rough Terrain", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][15] = array("used"=>TRUE, "tag"=>"", "display"=>"Low Bar", "inputlen"=>1, "maxlen"=>1, "default_value"=>"0");
$dispfields["Play"][16] = array("used"=>TRUE, "tag"=>"", "display"=>"Speed Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"5");
$dispfields["Play"][17] = array("used"=>TRUE, "tag"=>"", "display"=>"Pickup Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"5");
$dispfields["Play"][18] = array("used"=>TRUE, "tag"=>"", "display"=>"Low Shoot Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"5");
$dispfields["Play"][19] = array("used"=>TRUE, "tag"=>"", "display"=>"High Shoot Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"5");
$dispfields["Play"][20] = array("used"=>TRUE, "tag"=>"", "display"=>"Breach Rating", "inputlen"=>1, "maxlen"=>1, "default_value"=>"5");
// $dispfields["Play"][21] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][22] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][23] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][24] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][25] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][26] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][27] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][28] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][29] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][30] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][31] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][32] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][33] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][34] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][35] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][36] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][37] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][38] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Play"][39] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");


//
// Match Fields -- analyzed in each match.  Data specific to that match
//

$dispfields["Match"][0] = array("used"=>TRUE, "tag"=>"autoscorebot", "display"=>"autoscorebot", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][1] = array("used"=>TRUE, "tag"=>"autoscoretop", "display"=>"autoscoretop", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][2] = array("used"=>TRUE, "tag"=>"AutoRock Wall", "display"=>"AutoRock Wall", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][3] = array("used"=>TRUE, "tag"=>"AutoMoat", "display"=>"AutoMoat", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][4] = array("used"=>TRUE, "tag"=>"AutoDrawbridge", "display"=>"AutoDrawbridge", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][5] = array("used"=>TRUE, "tag"=>"AutoCheval de frise", "display"=>"AutoCheval de frise", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][6] = array("used"=>TRUE, "tag"=>"AutoLow Bar", "display"=>"AutoLow Bar", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][7] = array("used"=>TRUE, "tag"=>"startposition", "display"=>"startposition", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][8] = array("used"=>TRUE, "tag"=>"Misses High", "display"=>"Misses High", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][9] = array("used"=>TRUE, "tag"=>"Misses Low", "display"=>"Misses Low", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][10] = array("used"=>TRUE, "tag"=>"Percent Accuracy", "display"=>"Percent Accuracy", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][11] = array("used"=>TRUE, "tag"=>"driverrating", "display"=>"driverrating", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][12] = array("used"=>TRUE, "tag"=>"pickuprating", "display"=>"pickuprating", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][13] = array("used"=>TRUE, "tag"=>"BreacherRating", "display"=>"BreacherRating", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][14] = array("used"=>TRUE, "tag"=>"Rock Wall", "display"=>"Rock Wall", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][15] = array("used"=>TRUE, "tag"=>"Rock Wall Difficult?", "display"=>"Rock Wall Difficult?", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][16] = array("used"=>TRUE, "tag"=>"Moat", "display"=>"Moat", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][17] = array("used"=>TRUE, "tag"=>"Moat Difficult?", "display"=>"Moat Difficult?", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][18] = array("used"=>TRUE, "tag"=>"Drawbridge", "display"=>"Drawbridge", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][19] = array("used"=>TRUE, "tag"=>"Drawbridge Difficult?", "display"=>"Drawbridge Difficult?", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][20] = array("used"=>TRUE, "tag"=>"Cheval de frise", "display"=>"Cheval de frise", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][21] = array("used"=>TRUE, "tag"=>"Cheval de frise Difficult?", "display"=>"Cheval de frise Difficult?", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][22] = array("used"=>TRUE, "tag"=>"Low Bar", "display"=>"Low Bar", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][23] = array("used"=>TRUE, "tag"=>"Low Bar Difficult", "display"=>"Low Bar Difficult", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][24] = array("used"=>TRUE, "tag"=>"robottype", "display"=>"robottype", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][25] = array("used"=>TRUE, "tag"=>"Pickup Speed", "display"=>"Pickup Speed", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][26] = array("used"=>TRUE, "tag"=>"Pickup Stable", "display"=>"Pickup Stable", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][27] = array("used"=>TRUE, "tag"=>"telescorebot", "display"=>"telescorebot", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][28] = array("used"=>TRUE, "tag"=>"telescoretop", "display"=>"telescoretop", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][29] = array("used"=>TRUE, "tag"=>"TechFoul", "display"=>"TechFoul", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][30] = array("used"=>TRUE, "tag"=>"FinalPosit", "display"=>"FinalPosit", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][31] = array("used"=>TRUE, "tag"=>"redcard", "display"=>"redcard", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][32] = array("used"=>TRUE, "tag"=>"Disabled", "display"=>"Disabled", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][33] = array("used"=>TRUE, "tag"=>"Broken", "display"=>"Broken", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
$dispfields["Match"][34] = array("used"=>TRUE, "tag"=>"Notes", "display"=>"Notes", "inputlen"=>3, "maxlen"=>3, "default_value"=>"");
// $dispfields["Match"][35] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Match"][36] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Match"][37] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Match"][38] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");
// $dispfields["Match"][39] = array("used"=>TRUE, "tag"=>"", "display"=>"", "inputlen"=>1, "maxlen"=>1, "default_value"=>"");

//
// set max length of Match fields to match the database.  Tablet data longer than this is truncated.
$tablet_max_matchfield = 50;

// map match tags to standard columns
//  Format: tag=>column
$map_match_tags = array ("offensiverating"=>"rating_offense", "defensiverating"=>"rating_defense");

//
// score breakout
//
// map stats tags to columns
//  Format: tag=>column
$map_stats_tags = array ("opr"=>"opr","dpr"=>"dpr","ccwm"=>"ccwm");


//
// Ranking: custom columns to display
//
//  Format:
//   key=>array, array of arrays that include:
//     - key: db column name
//     - array:
//       - display text
//       - format mask to be used in printf
//$rank_columns_custom  = array (
//  array( "column"=>"opr", "heading"=>"Off Power", "format"=>"%.2f"),
//  array( "column"=>"dpr", "heading"=>"Def Power", "format"=>"%.2f"),
//  array( "column"=>"ccwm", "heading"=>"CCW Marg", "format"=>"%.2d")
//  );
  
$rank_columns_custom  = array (
  "opr"=>array("heading"=>"Off Power", "format"=>"%.2f"),
  "dpr"=>array("heading"=>"Def Power", "format"=>"%.2f"),
  "ccwm"=>array("heading"=>"CCW Marg", "format"=>"%.2d")
  );
 

// ***************************
//
// File locations for all file directives in system
//
// Initial section specifies files used in Windows
//  and may also work for Linux.  Then comes a "Linux
//   Overide" section where variables can be overwritten 
//   if the system operates on a non-windows platform
//

// Default Files Locations

  // location of security files and routines

  $htpasswdfile = "../admin/htpasswd.master";
  $htpasswdexe = "../bin/htpasswd.exe";
  // $htpasswdexe = "C:\apps\Apache24\bin\htpasswd.exe";

  //
  // Schedule files and documentation
  //
  $schedule_xls = "doc/qualification_schedule.xls";
  $schedule_csv = "doc/qualification_schedule.csv";
  
  // 
  // log directory
  //
  $logdir = "log";
  
  //
  // auto-update file indicator
  //  If set to 1, turns on every 5 min updates from tBA
  //
  $auto_update = "sysfiles/autoupdate.ctl";
  
  //
  // Tablet Server Home directory
  //

  $tablet_server_home = "c:/tabletfiles/tabletserver";

//
// Linux File Location Overrides
if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') 
{
  
  //
  // Tablet Server Home directory
  //
  
  $tablet_server_home = "/home/roboload/TabletServer";

} 
// end of Linux file location overrides


//
// Tablet Server File Locations, based on $tablet_server_home
//

$tablet_templates = $tablet_server_home . "/templates";
$tablet_ingest_match = $tablet_server_home . "/ingest-match";
$tablet_ingest_pit = $tablet_server_home . "/ingest-pit";
$tablet_ingest_complete = $tablet_server_home . "/ingest-complete";
$tablet_ingest_error = $tablet_server_home . "/ingest-error";
$tablet_teamimages = $tablet_server_home . "/teamimages";
$tablet_export = $tablet_server_home . "/export";


// default user password (filled in)
$default_password = "wild4woodside";

// administrator users
$admins = array("jlv", "mckay");


//
// Message  timing for message recv application
//

$message_refresh = 6;


//
// Database parameters
//

$dbname = "competition";

$dbuser = "compuser";

$dbpass = "100hats";

$dbhost = "localhost";


// ***************************
//
// Misc App Configuration
//

// global default font
$font_sys_default = "arial";
//$font_sys_default = "helvetica";
  
// background colors used for red and blue teams
$background_colors = array ("R"=>"#ffaaaa", "Red"=>"#ffaaaa", "B"=>"#aaaaff", "Blue"=>"#aaaaff");

// background colors for other areas
$lyellow = '#ffff66';
$lgreen = '#ccffcc';
$lblue = '#ccccff';
$lred = '#ffcccc';

//length of the updated-by field.  Should not exceed db schema
$maxlengthchangeby=200;


// ***************************
//
// The Blue Alliance API mapping fields
//
// Field mappings to the API are defined in bluealliance.inc for performance reasons
//


// *****************************************************************
//
// Parameterized / Custom Functions
//
// Functions below are generally game-specific and must be written 
//  for each year
//


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
// end of params.inc -- do not leave any lines under the end of this closure or html is not well-formed with top header line
//
?>