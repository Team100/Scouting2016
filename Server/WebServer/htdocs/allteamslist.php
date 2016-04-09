<?php
// $Revision: 3.0 $
// $Date: 2016/03/14 22:56:41 $
//
// Competition System - All Teams List
//

require "page.inc";
pheader("All Teams List - Competition System - " . $host_team_name);
$connection = dbsetup();


// check parameter and update if needed
if (isset($_GET['needseval'])) set_user_prop("needeval", $_GET['needseval']);
// check value
$needseval = test_user_prop("needeval");

// set up for needs eval
// if $needeval, then get array for bullets
if ($needseval == 1) $teams_need_eval = allteams_need_eval();

// show banner
print <<< EOF_EOF
<a href="/">Return to Home</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;
EOF_EOF
; // end of print

// show needs eval feature on/off link
print "<a href=\"/allteamslist.php?needseval=";
if ($needseval == 1) print "0\">Hide"; else print "1\">Show";
print "Needs Eval</a>\n";

// set up table
print <<< EOF_EOF

<!--- Table Listing --->

<table valign="top">
<tr valign="top">
<td>

<!--- Teams Section --->
<table valign="top">

<tr valign="top">
<td>
<table border="2">
EOF_EOF
; // end of print

  // find total team count and set page break
  if (!($result = @ mysqli_query ($connection, "select count(*) total from team")))
    dbshowerror($connection);
  $row = mysqli_fetch_array($result);
  $total = $row["total"];
  $pagebreak = ceil ($total / 3);   	// ceil rounds up


  // define result set
  if (!($result = @ mysqli_query ($connection, "select teamnum, name, nickname from team order by teamnum")))
    dbshowerror($connection);

  $rowcnt=1;
  while ($row = mysqli_fetch_array($result))
   {
    // print each row with href
    print "<tr><td><a href=\"/teamdetails.php?teamnum={$row["teamnum"]}\">{$row["teamnum"]}";
    if (in_array($row["teamnum"], $teams_need_eval)) print "&bull;";
    print "- {$row["name"]} ";
     // add nickname if it exists
     // if ($row["nickname"]) print "({$row["nickname"]})";
     print "</a></td></tr>\n";

    // if more than pagebreak rows, pagenate
    if (! ($rowcnt++  % $pagebreak  ))

      {
        // end last table, move next cell, start another table
        print "</table></td><td><table border=\"2\">\n";
        //$rowcnt = 0;
      }
    }
?>
</table>
</td>
</tr>
</table>


<?php
   pfooter();
 ?>
