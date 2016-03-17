<?php
	// $Revision: 3.0 $
	// $Date: 2016/03/14 22:56:41 $
	//
	// Competition System - documentation add
	//
	require "page.inc";

	pheader("Add Documentation");

	$connection = dbsetup();

	$edit=$_GET["edit"];
	//always in edit mode
	// 2 if input

	print "<a href=\"/documentationhome.php\">Documentation Home</a><br>";

	$fields = array("documentation", "topic", "priority");
	$fieldsizes = array(documentation=>20, topic=>20, priority=>2);

	print "<br>Available topics: ";

	if (! ($result=@mysqli_query ($connection, "select * from topic order by priority") ))
		dbshowerror($connection, "die");
	while($row=mysqli_fetch_array($result))
		print $row["topic"].", ";


	if($admin)
	{
		print "<form method=\"POST\" action=\"/newdocumentation.php?edit=2\">";

		print "<table><tr>";
		foreach($fields as $title)
			if($title=="documentation")
				print "<td>{$title}*</td>";
			else
				print "<td>{$title}</td>";
		print "</tr><tr>";
		foreach($fields as $title)
			print "<td><input type=\"text\" name=\"{$title}input\" size={$fieldsize[$title]} maxlength={$fieldsize[$title]}></td></td>";
		print "</table>Page: <input type=\"text\" name=\"page\" size=10 maxlength=20>
			<br><br>Documentation*:<br><input type=\"text\" name=\"datainput\" size=5000 maxlength=5000><br>";

		print "<INPUT TYPE=\"submit\" name=\"Submit\" VALUE=\"Submit\" ALIGN=middle BORDER=0>";

		if($edit == 2)
		{
			if(isset($_POST["documentationinput"]) && $_POST["documentationinput"]!="" && isset($_POST["datainput"]) && $_POST["datainput"]!="")
			{
				$query="insert into documentation (";
				foreach($fields as $name)
					if(isset($_POST[$name."input"]) && $_POST[$name."input"]!="")
						$query=$query."{$name}, ";
				$query=$query."data) values (";
				foreach($fields as $name)
					if(isset($_POST[$name."input"]) && $_POST[$name."input"]!="")
						$query=$query."'{$_POST[$name."input"]}', ";
				$query=$query."'{$_POST["datainput"]}')";

				if (! (mysqli_query ($connection, $query) ))
					dbshowerror($connection, "die");

				$pagetemp=$_POST["page"];
				if(isset($pagetemp) && $pagetemp!="")
					if (! (mysqli_query ($connection, "insert into pagetodoc (documentation, page) values ('{$_POST["documentationinput"]}', '{$pagetemp}')") ))
						dbshowerror($connection, "die");

				print "<br><b>Documentation Added</b>";
			}
			else
			{
				print "<br><font color=red><b>Invalid data entered</font>";
			}
		}
	}
	else
		print "You must be an admin to add documentation";

	pfooter();
?>