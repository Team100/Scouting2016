<?php
// $Revision: 3.0 $
// $Date: 2016/03/14 22:56:41 $
//
// Competition System - Main Page
//
//

require "page.inc";
pheader("Home -  " . $host_team_name . " - Competition System");
$connection = dbsetup();

// check parameter and update if needed
if (isset($_GET['needseval'])) set_user_prop("needeval", $_GET['needseval']);
// check value
$needseval = test_user_prop("needeval");

// set up for needs eval
// if $needeval, then get array for bullets
if ($needseval == 1) $teams_need_eval = allteams_need_eval();

//
// start display
//
print "
<a href=\"/allteamslist.php\">All Teams</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;
"; // end of print

// show needs eval feature on/off link
print "<a href=\"/?needseval=";
if ($needseval == 1) print "0\">Hide"; else print "1\">Show";
print " Needs Eval</a>\n";

print "
<table valign=\"top\">
<tr valign=\"top\">
<td>

<!--- Teams Section --->
<table valign=\"top\">

<tr valign=\"top\">
<td>
<table border=\"2\">
"; // end of print

  // find total team count and set page break
  if (!($result = @ mysqli_query ($connection, "select count(teamnum) total from teambot where event_id = '{$sys_event_id}'")))
    dbshowerror($connection);
  $row = mysqli_fetch_array($result);
  $total = $row["total"];
  $pagebreak = ceil ($total / 2);   	// ceil rounds up


  // define result set
  $query = "select team.teamnum teamnum, name, nickname from team, teambot where team.teamnum = teambot.teamnum
      and teambot.event_id = '{$sys_event_id}' order by team.teamnum";

  if (debug()) print "<br>DEBUG-index: " . $query . "<br>\n";
  if (!($result = @mysqli_query ($connection, $query)))
    dbshowerror($connection);

  $rowcnt=1;
  while ($row = mysqli_fetch_array($result))
   {
    // print each row with href
    print "<tr><td>" . teamhref($row["teamnum"]) . "{$row["teamnum"]}";
    if (in_array($row["teamnum"], $teams_need_eval)) print "&bull;";
    print " - " . substr($row["name"], 0, $team_name_display_max-6);
     // add nickname if it exists
     if ($row["nickname"]) print " " . substr($row["nickname"], 0, 12);
     print "</a></td></tr>\n";

    // if more than pagebreak rows, pagenate
    if (! ($rowcnt++  % $pagebreak  ))
        // end last table, move next cell, start another table
        print "</table></td><td><table border=\"2\">\n";
   }

// end table
print "
</table>
</td>
</tr>
</table>
</td>


<!--- Blank column --->
<td>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</td>

<!--- Functions Section --->
<td valign=\"top\">
<h3><center><u>Functions</u></center></h3>
<ul>
<li><a href=\"/matchlist.php\">Match Listings</a></li>
<li><a href=\"/matchlist.php\">Evaluate a match</a></li>
<li><a href=\"/matchnew.php?edit=1\">Create new match</a></li>
<br>
<li><a href=\"/rank.php\">Rank Teams</a></li>
<br>
<li><a href=\"/finalselect.php\">Finals Selection - In Stands</a></li>
<li><a href=\"/finalselectfield.php\">Finals Selection - On Field</a></li>
<br>
<li><a href=\"/matchlist.php?filter=F\">Evaluate a finals match</a></li>
<br>
<li><a href=\"/tabletsync.php\">Sync with tablet servers</a></li>

<br>
<li><a href=\"/messagesend.php\">Send a Message to Field</a></li>
<li><a href=\"/messagerecv.php\">Receive a Message in Field</a></li>
</ul>

<!--- Documentation Section --->
<h3><center><u>Documentation</u></center></h3>
<ul>
<li><a href=/doc/photos.php>Creating photos</a>
  &nbsp;&nbsp;<a href=\"/doc/PhotoLog.pdf\">Photo Log</a>
  </li>
<li><a href=\"/<?php print $schedule_xls ?>\">Qualification Schedule (xls)</a></li>
<li><a href=\"/documentationhome.php\">Documentation</a></li>
</ul>


<!--- Admin Section --->

"; // end of print

  // if administrator, show admin section.  Otherwise skip
  if ($admin)
  {
    print "<h4><center><a href=\"/admin.php\">Sys Admin Functions</a></center></h4>\n";
    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Software Version: {$release_version}<br>\n";
  }


print "
</td>
</tr>
</table>
"; // end of print


   pfooter();
 ?>
