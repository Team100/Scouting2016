<?
 /*
   PHP Test file
 */

include ('httpful.phar');

$ourteam='frc0100';
$tbaAppId= $ourteam . ':compsystem:v02';

$queryteam='frc3006';
$year='2014';

print $tbaAppId . "\n\n";

$url = "http://www.thebluealliance.com/api/v2/event/2016scmb/rankings";

$response = \Httpful\Request::get($url)
    ->addHeader('X-TBA-App-Id',$tbaAppId)
    ->send();



print_r ($response);

printf ("\n\n");

?>
