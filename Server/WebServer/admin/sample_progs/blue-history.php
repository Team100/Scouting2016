<?php
 /*
   PHP Test file
 */

//include ('httpful.phar');
include ('../../htdocs/lib/httpful.phar');

$ourteam='frc0100';
$tbaAppId= $ourteam . ':compsystem:v02';

$queryteam='frc3006';
$year='2014';

print $tbaAppId . "\n\n";

$url = "www.thebluealliance.com/api/v2/team/{$queryteam}/history/awards";

$response = \Httpful\Request::get($url)
    ->addHeader('X-TBA-App-Id',$tbaAppId)
    ->send();



print_r ($response);

printf ("\n\n");

?>