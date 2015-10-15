<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/KS/THAILANDPOST/Track.php';

/**
 * @property HTTP $Http
 */
class TrackTest extends PHPUnit_Framework_TestCase {

    private $Tracker = null;

    public function __construct() {
        $this->Tracker = new \KS\THAILANDPOST\Track('/tmp/cookie.txt');
    }

    public function testGetTracks() {
        //invalid ems tracker
        $ems = 'E111717744TH';
        $trackers = $this->Tracker->getTracks($ems);
        $this->assertEquals(false, $trackers);
        
        //invalid ems tracker
        $ems = 'EN1117177XXTH';
        $trackers = $this->Tracker->getTracks($ems);
        $this->assertEquals(false, $trackers);
        
        //Success
        $ems = 'ED883533903TH';
        $trackers = $this->Tracker->getTracks($ems);
        $this->assertEquals(true, is_array($trackers));
        $this->assertEquals(8, count($trackers));
        
        foreach ($trackers as $tracker) {
            $this->assertArrayHasKey('date', $tracker);
            $this->assertArrayHasKey('location', $tracker);
            $this->assertArrayHasKey('description', $tracker);
            $this->assertArrayHasKey('status', $tracker);
        }
        
        //Check Eng language
        $this->Tracker->enableEngLanguage();
        $trackers = $this->Tracker->getTracks($ems);
        $this->assertEquals(true, is_array($trackers));
        $this->assertEquals(8, count($trackers));
        
        foreach ($trackers as $tracker) {
            $this->assertRegExp('/[a-zA-Z0-9 ]/', $tracker['date']);
            $this->assertRegExp('/[a-zA-Z0-9 ]/', $tracker['location']);
            $this->assertRegExp('/[a-zA-Z0-9 ]/', $tracker['description']);
            $this->assertRegExp('/[a-zA-Z0-9 ]?/', $tracker['status']);
        }
        
        //Success other case
        $ems = 'EN136288445TH';
        $trackers = $this->Tracker->getTracks($ems);
        $this->assertEquals(true, is_array($trackers));
        $this->assertEquals(9, count($trackers));
        
        foreach ($trackers as $tracker) {
            $this->assertArrayHasKey('date', $tracker);
            $this->assertArrayHasKey('location', $tracker);
            $this->assertArrayHasKey('description', $tracker);
            $this->assertArrayHasKey('status', $tracker);
        }
        
        //Check Eng language
        $this->Tracker->enableEngLanguage();
        $trackers = $this->Tracker->getTracks($ems);
        $this->assertEquals(true, is_array($trackers));
        $this->assertEquals(9, count($trackers));
        
        foreach ($trackers as $tracker) {
            $this->assertRegExp('/[a-zA-Z0-9 ]/', $tracker['date']);
            $this->assertRegExp('/[a-zA-Z0-9 ]/', $tracker['location']);
            $this->assertRegExp('/[a-zA-Z0-9 ]/', $tracker['description']);
            $this->assertRegExp('/[a-zA-Z0-9 ]?/', $tracker['status']);
        }
    }
    

}
