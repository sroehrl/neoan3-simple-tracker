<?php

use Neoan3\Apps\SimpleTracker;

require_once '../vendor/autoload.php';
require_once '../SimpleTracker.php';

$_SERVER['REQUEST_URI'] = 'http://test.com';
$db = SimpleTracker::init();
//$db->get('https://test.com')->delete();
//SimpleTracker::track();
$t = $db->get('http://test.com')->filter('visits','rand-NiPaV', function($item,$find){
    return (($item['identifier'] == $find) ? $item : false);
});
var_dump($t);
die();
var_dump(SimpleTracker::endpointData('http://test.com'));
