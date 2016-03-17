<?php
  // $Revision: 2.1 $
  // $Date: 2011/04/07 06:14:12 $
  //
  // Competition System - Main Page
  //
  require "..\page.inc";
  pheader("Team Photos Documentation");
  ?>


Team Robot Images are placed in this directory in with names in the
following format so they can be read automatically by the application:

<ul>
<li>Small Thumbnail used on multiple pages/listings 50px
<br>
   File format: team-{TEAM#}-small.jpg    (Example: team-3006-small.jpg)

<li>Medium Thumbnail used on team information pages:180px
<br>  File format:  team-{TEAM#}-med.jpg    (Example: team-3006-med.jpg)

<li>Other images of the robot (up to 5):(800-1200 px wide)
<be>  File format:  team-{TEAM#}-{Image#}.jpg    (Example: team-3006-1.jpg)
</ul>

These images are picked up by the teaminfo and teamdetails pages.
<p>
It is very helpful when taking photos to take a photo of the team number on the robot or some other prominent
place so that the team number starts the sequence of photos of the team robot.  This way the user is not left
guess which photo applies to which robots.
<p>
It may by helpful to us the following <a href="/doc/PhotoLog.pdf">Photo Log</a> as you capture photos.

<?php
  pfooter();
  ?>
