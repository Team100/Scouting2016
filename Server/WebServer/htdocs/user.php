<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Administer Users
  //
  require "page.inc";

  // get variables
  $edit=$_GET["edit"];

  // header and setup
  pheader("User Administration");

  // handle update if needed
  if ($edit == 2)
  {
  	// load form fields
  	$newuser=$_POST["newuser"];
    $newpass=$_POST["newpass"];
    $op=$_POST["op"];

    if ($op == "Delete")
    {
       $lastline = system("{$htpasswdexe} -D \"{$htpasswdfile}\" {$newuser}", $retval);
       if ($retval)
          print "<b>User delete operation failed.</b><br>\n";
       else
          print "<b>Deleted user {$newuser}.</b><br>\n";
    }
    else
    {
       $lastline = system("{$htpasswdexe} -b \"{$htpasswdfile}\" {$newuser} {$newpass}", $retval);
       if ($retval)
	      print "<b>User operation failed.</b><br>\n";
	   else
          print "<b>Updated user {$newuser}.</b><br>\n";
    }

	if ($lastline) print "<br><b>{$lastline}</b>\n";

    // update completed
    $edit = 0;
  }

?>

<!----- Top of page ----->
<table valign="top">
<tr valign="top">
<td>

<!--- Input table --->
<form method="POST" action="/user.php?edit=2">
<table valign="top">

<tr valign="top">
<td>Username:&nbsp; </td>
<td>
<input type="text" name="newuser" size=10 maxlength=12>
</td></tr>

<td>Password:&nbsp; </td>
<td>
<input type="text" name="newpass" size=12 maxlength=16 value="<?php print $default_password; ?>" >
</td></tr>

</table>
<INPUT TYPE="submit" name="op" VALUE="Add or Save" ALIGN=middle BORDER=0>
&nbsp; &nbsp;
<INPUT TYPE="submit" name="op" VALUE="Delete" ALIGN=middle BORDER=0>
</form>
</table>

<br>
<br>
<a href="/">Return to Home</a>
<br>

<?php
   pfooter();
  ?>
