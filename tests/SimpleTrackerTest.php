<?php


namespace Neoan3\Apps;
require_once '../SimpleTracker.php';
require_once '../vendor/autoload.php';

use Filebase\Database;
use Filebase\Document;
use PHPUnit\Framework\TestCase;

/**
 * Class SimpleTrackerTest
 *
 * @package Neoan3\Apps
 */
class SimpleTrackerTest extends TestCase
{
    /**
     * @var string
     */
    private $location   = 'https://test.com/test';
    /**
     * @var string
     */
    private $identifier = 'Tester';
    /**
     * @var Database
     */
    private $instance;

    /**
     *
     */
    public function setUp(): void
    {
        $this->instance = SimpleTracker::init();
    }

    /**
     *
     */
    public function testInit()
    {
        $db = $this->instance;
        $this->assertInstanceOf(Database::class,$db);
    }


    /**
     *
     */
    public function testTrack(){
        $_SERVER['REQUEST_URI'] = $this->location;
        $db = SimpleTracker::track($this->identifier);
        $this->assertInstanceOf(Database::class,$db);
    }

    /**
     *
     */
    public function testEndpointData(){
        $visits = SimpleTracker::endpointData($this->location)->toArray();
        $this->assertArrayHasKey('visits',$visits);
    }

    /**
     *
     */
    public function testIdentifierData(){
        $user = SimpleTracker::identifierData($this->identifier);
        $this->assertInstanceOf(Document::class, $user);
    }

    /**
     *
     */
    public function testQueryTime(){
        $today = date('Y-m-d');
        $newest = $this->instance->query()->where('date','>',$today)->results();
        $this->assertIsArray($newest);
        foreach ($newest as $hit){
            $this->assertStringStartsWith($today,$hit['date']);
        }

    }

    /**
     *
     */
    public function testDelete(){
        $this->assertTrue($this->instance->truncate());
    }

}
