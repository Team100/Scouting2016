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

  // if not administrator, display error.  Otherwise show admin section.
  if (! $admin)
    print "<h3>You must be an administrator to use this page.</h3>\n";
  else
  {

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

//
// format page
print <<< EOF_EOF
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
<input type="text" name="newpass" size=12 maxlength=16 value="{$default_password}" >
</td></tr>

</table>
<INPUT TYPE="submit" name="op" VALUE="Add or Save" ALIGN=middle BORDER=0>
&nbsp; &nbsp;
<INPUT TYPE="submit" name="op" VALUE="Delete" ALIGN=middle BORDER=0>
</form>
</table>

EOF_EOF
; // end of print
} // end of admin

print "
<br>
<br>
<a href=\"{$base}\">Return to Home</a>
";

  if ($admin) print "&nbsp;&nbsp;&nbsp; <a href=\"/admin.php\">Sys Admin</a>\n";
  print "<br>\n";

   pfooter();
  ?>