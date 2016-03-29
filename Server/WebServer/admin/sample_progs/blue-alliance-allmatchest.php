<?php
//
// REST API test
//

// make sure to run where you have an httpful.phar

include ('httpful.phar');
include ('../../htdocs/lib/httpful.phar');

$ourteam='frc0100';
$tbaAppId= $ourteam . ':compsystem:v02';

$queryteam='frc3006';
//$queryteam='frcx3006';

$event="2016cada";
$event="2016calb";

print $tbaAppId . "\n\n";

$url = "http://www.thebluealliance.com/api/v2/event/{$event}/matches";

try
{
  $response = \Httpful\Request::get($url)
      ->addHeader('X-TBA-App-Id',$tbaAppId)
      ->send();
} catch (Exception $e) {
  print 'Caught exception: ' . $e->getMessage() . "\n";
}

print "Response:\n";

print_r ($response);

printf ("\n\n");

//
// sample loop through data
//

foreach($response->body as $key=>$value)
{
        print "Element: " . $key . ", " . $value->comp_level
          . ", " . $value->match_number . "\n";
}


printf ("\n\n");

?>