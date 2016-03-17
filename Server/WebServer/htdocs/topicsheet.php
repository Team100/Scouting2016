<?php
	// $Revision: 3.0 $
	// $Date: 2016/03/14 22:56:41 $
	//
	// Competition System - list of topics, for admins only
	//
	require "page.inc";

	pheader("Topic Sheet");

	$connection = dbsetup();

	$edit=$_GET["edit"];
	$steal=$_GET["dblocksteal"];

	$dblock = array(table=>"process_lock",where=>"lock_id = 'doc_topics'");
	$fields = array("topic", "priority", "description");
	$fieldsizes = array(topic=>20, priority=>2, description=>200);

	if($steal==1)//steal this table
		if (! (@mysqli_query ($connection, "update process_lock set locked='{$user}' where lock_id='doc_topics'") ))
			dbshowerror($connection, "die");

	if (! ($result=@mysqli_query ($connection, "select locked from process_lock where lock_id='doc_topics'") ))
		dbshowerror($connection, "die");
	$row = mysqli_fetch_array($result);
	$dbcontrol=$row["locked"];

	if($dbcontrol && $dbcontrol!=$user)
	{
		if($edit)
		{
			showerror("This table is being edited- steal the page if you wish to continue");
			print "<br>";
		}//editing without permision

		$edit=0;
	}//no permission, give the option to steal the page
	if(!$dbcontrol && $edit)
	{
		dblock($dblock, "lock");
		$dbcontrol=$user;
	}//take control if there is no control

	//
	// edits:
	// 0: no edit
	// 1: edit topics
	// 2: enter data and leave
	//

	if($edit==2)
	{
		if (! ($result=@mysqli_query ($connection, "select * from topic") ))
			dbshowerror($connection, "die");
		$total=0;
		while($row = mysqli_fetch_array($result))
			$total++;

		if (! (mysqli_query ($connection, "delete from topic") ))
			dbshowerror($connection, "die");

		$count=0;
		while($count<=$total)
		{
			$topic=$_POST["topic".$count];
			if(isset($topic) && $topic!="")
			{
				$query="insert into topic (topic";
				foreach($fields as $name)
					if($name!="topic")
						if(isset($_POST[$name.$count]) && $_POST[$name.$count]!="")
							$query=$query.", {$name}";
				$query=$query.") values ('{$topic}'";
				foreach($fields as $name)
					if($name!="topic")
						if(isset($_POST[$name.$count]) && $_POST[$name.$count]!="")
							$query=$query.", '{$_POST[$name.$count]}'";
				$query=$query.")";

				if (! (mysqli_query ($connection, $query) ))
					dbshowerror($connection, "die");

				if($count==$total)
					print "Topic '{$topic}' entered<br>";
			}
			else if($count!=$total)
				print "Empty topic: column deleted";
			$count++;
		}

		$edit=0;
	}//enter data

	print "<a href=\"/documentationhome.php\">Documentation Home</a><br>";
	//print "<a href=\"/topicsheet.php\">Topic Home</a><br>";

	if($edit)
	{
		print "<a href=\"/topicsheet.php\">Cancel Editing</a><br>";
		if(!$dbcontrol)
			dblock($dblock, "lock");
	}
	else if($dbcontrol==$user)
	{
		dblock($dblock, "abandon");
		$dbcontrol=null;
	}//remove your own control if not editing
	if(!$edit && !$dbcontrol)
		print "<a href=\"/topicsheet.php?edit=1\">Edit this page</a><br>";
	else if($dbcontrol && $dbcontrol!=$user)
	{
		print "Locked by {$dbcontrol}- <a href=\"/topicsheet.php?dblocksteal=1&edit=1\">!Steal the page!</a><br>";
	}//page stealing
//end of dblock

	if($edit)
	{
		print "<form method=\"POST\" action=\"/topicsheet.php?edit=2\">";
	}

	print "<br><b>Topics:</b><br><table border=1><tr>";
	foreach($fields as $title)
		print "<td>{$title}</td>";
	print "</tr>";

	if (! ($result=@mysqli_query ($connection, "select * from topic order by priority") ))
		dbshowerror($connection, "die");

	$count=0;
	while($row = mysqli_fetch_array($result))
	{
		print "<tr>";
		foreach($fields as $title)
		{
			if($edit)
				print "<td><input type=\"text\" name=\"{$title}{$count}\" size={$fieldsize[$title]} maxlength={$fieldsize[$title]} value='".$row[$title]."'></td></td>";
			else
				print "<td>".$row[$title]."</td>";
		}
		$count++;
		print "</tr>";
	}

	if($edit)
	{
		print "<tr><td>Enter a new topic:</td></tr><tr>";

		foreach($fields as $title)
			print "<td><input type=\"text\" name=\"{$title}{$count}\" size={$fieldsize[$title]} maxlength={$fieldsize[$title]}\"></td>";
	}

	print "</table>";

	if($edit)
		print "<INPUT TYPE=\"submit\" name=\"Submit\" VALUE=\"Submit\" ALIGN=middle BORDER=0></form>";


	pfooter();
?>