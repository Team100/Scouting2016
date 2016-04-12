<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System - receive message
  //

  require "page.inc";
  // get variables

  pheader("Receive Message", "titleonly", array ("openhead"=>1) );
  $connection = dbsetup();

  // add retrieve header
  print "<meta http-equiv=\"refresh\" content=\"{$message_refresh}\">\n";

  // close head and body
  print "</HEAD>\n<BODY>\n";



  // get message
  if (! ($result = @ mysqli_query ($connection, "select message from message where facility = 'finals_selection'" ) ))
		dbshowerror($connection, "die");

  $message = mysqli_fetch_array($result);

   print "<p style=\"font-size:50px;\">{$message["message"]}</p>";


   pfooter();
 ?>
