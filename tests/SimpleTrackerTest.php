<?php


namespace Neoan3\Apps;
require_once '../SimpleTracker.php';
require_once '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class SimpleTrackerTest extends TestCase
{

    public function testInit()
    {
        $this->assertTrue(SimpleTracker::init());
    }
    public function testTrack(){
        SimpleTracker::track();
    }
}
