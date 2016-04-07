<?php
  // $Revision: 3.0 $
  // $Date: 2016/03/14 22:56:41 $
  //
  // Competition System
  //
  // Sets event code and name in database
  // Event code is used to qualify all tables so we can handle multiple regionals in the same database
  // Event code is also used on a number of Blue Alliance API calls.  Blue Alliance loader won't work without it.
  //
  // Confirms event code with Blue Alliance data, Then sets in our database.
  //

  require "page.inc";
  // header and setup

  $connection = dbsetup();


  $query = "select teamnum from teambot where teamnum = {971, 1072, 1678}";

   if (!($result = @mysqli_query ($connection, $query)))
        dbshowerror($connection);
   while ($row = mysqli_fetch_array($result))
   {
       print $row['teamnum'] . "\n";

    }h

exit;
