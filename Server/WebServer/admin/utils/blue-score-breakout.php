<?php
//
//
//
// Blue Alliance utility to inspect rankings and show tag
//

include ('../../htdocs/lib/httpful.phar');

$year='2016';
$event='cada';

$ourteam='frc0100';
$tbaAppId= $ourteam . ':compsystem:v02';

print "AppID:" . $tbaAppId . "\n\n";

$url = "http://www.thebluealliance.com/api/v2/event/{$year}{$event}/matches";


$response = \Httpful\Request::get($url)
    ->addHeader('X-TBA-App-Id',$tbaAppId)
    ->send();

print "Response from Blue Alliance - Score Breakdown\n";
print $url . "\n";
//print_r ($response);

print "\nYear: {$year}\n";
print "Event: {$event}\n";
print "\n\nScore Breakdown Array:\n";
print_r($response->body[0]->score_breakdown->blue);

print "\n\nend.\n";

?>