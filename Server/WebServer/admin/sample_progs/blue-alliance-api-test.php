<?php
//
// REST API test
//

// make sure to run where you have an httpful.phar

//include ('httpful.phar');
include ('../../htdocs/lib/httpful.phar');

$ourteam='frc0100';
$tbaAppId= $ourteam . ':compsystem:v02';

$queryteam='frc3006';
//$queryteam='frcx3006';

print $tbaAppId . "\n\n";

$url = "http://www.thebluealliance.com/api/v2/team/{$queryteam}";

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

print "Attribute: nickname: ";
print $response->body->nickname;
print "\n";

// test with variable as attribute
$attr='nickname';
print "Attribute by reference: nickname: ";
print $response->body->$attr;


printf ("\n\n");

?>