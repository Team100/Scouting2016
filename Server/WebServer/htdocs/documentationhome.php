<?php
	// $Revision: 3.0 $
	// $Date: 2016/03/14 22:56:41 $
	//
	// Competition System - documentation home
	//
	require "page.inc";

	pheader("Documentation Home");
	$connection = dbsetup();

	if($admin)
	{
		print "<a href=\"/topicsheet.php\">Topic Information</a><br>";
		print "<a href=\"/newdocumentation.php\">Add Documentation</a>";
	}

	print "<br><br><table>";

	if (! ($result=@mysqli_query ($connection, "select * from topic order by priority") ))
		dbshowerror($connection, "die");
	while($row=mysqli_fetch_array($result))
	{
		print "<tr><td><b>Topic: {$row["topic"]}</b></td></tr><tr><td>&nbsp Description: {$row["description"]}</td></tr>";
		if (! ($result2=@mysqli_query ($connection, "select * from documentation where topic='{$row["topic"]}' order by priority") ))
			dbshowerror($connection, "die");
		while($row2=mysqli_fetch_array($result2))
			print "<tr><td>&nbsp&nbsp&nbsp&nbsp&nbsp<a href=\"/documentation.php?page={$row2["documentation"]}\">Doc: {$row2["documentation"]}</a></td></tr>";
	}//loop of all the topics

	print "<tr><td><b>No Topics:</b></td></tr><tr><td>&nbsp;The topic for this documentation was not found in the table of topics</td></tr>";

	if (! ($result2=@mysqli_query ($connection, "select documentation from documentation where topic not in (select topic from topic) order by priority") ))
		dbshowerror($connection, "die");
	while($row2=mysqli_fetch_array($result2))
		print "<tr><td>&nbsp&nbsp&nbsp&nbsp&nbsp<a href=\"/documentation.php?page={$row2["documentation"]}\">Doc: {$row2["documentation"]}</a></td></tr>";

	print "</table>";

	pfooter();
?>