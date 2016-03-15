<?php
  // $Revision: 2.0 $
  // $Date: 2010/04/22 04:01:13 $
  //
  // Competition System - Evaluate Teams
  //
  require "page.inc";

  // get variables
  $teamnum=$_GET["team"];

  // header and setup
  pheader("Team Details - " . $teamnum);
  $connection = dbsetup();


  ?>


<!--- Teams Section --->
<table valign="top">
<tr>
<center><h3><u>Matches</u></h3><center>
</tr>

<tr valign="top">
<td>
<table border="2">

<?php
  // define result set
  if (!($result = @ mysqli_query ($connection, "select teamnum, name, nickname from team order by teamnum")))
    dbshowerror($connection);

  $rowcnt=0;
  while ($row = mysqli_fetch_array($result))
   {
    // print each row with href
    print "<tr><td><a href=\"\">{$row["teamnum"]}</a></td></tr>\n";

    // if more than 25 rows, pagenate
    if ( $rowcnt++ > 25 )
      {
        // end last table, move next cell, start another table
        print "</table></td><td><table border=\"2\">\n";
        $rowcnt = 0;
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
