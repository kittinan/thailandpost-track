<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/KS/THAILANDPOST/Track.php';

use PHPUnit\Framework\TestCase;

/**
 * @property HTTP $Http
 */
class TrackTest extends TestCase {

    private $Tracker = null;

    public function setUp() {
        $chrome_bin = getenv('TRAVIS_CHROME') ?: 'chromium-browser';

        $proxy_list = $this->getProxyList();
        $proxy = $proxy_list[array_rand($proxy_list, 1)];

        $this->Tracker = new \KS\THAILANDPOST\Track($chrome_bin, $proxy);
        $this->Tracker->setTimeout(30000);
    }

    public function tearDown() {
        unlink('/tmp/proxy.html');
    }

    public function getProxyList() {

        $cache_path = '/tmp/proxy.html';
        if (file_exists($cache_path) == false) {
            // Download proxy list
            $url = 'https://asia-northeast1-mr-kittinan.cloudfunctions.net/thaiproxy';
            $html = file_get_contents($url);
            file_put_contents($cache_path, $html);
        }

        $html = file_get_contents($cache_path);
        $ips = explode("\n", $html);
        return $ips;
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
        $ems = 'EW650052642TH';
        $trackers = $this->Tracker->getTracks($ems);
        $this->assertEquals(true, is_array($trackers));
        $this->assertEquals(11, count($trackers));
        
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
        $this->assertEquals(11, count($trackers));
        
        foreach ($trackers as $tracker) {
            $this->assertRegExp('/[a-zA-Z0-9 ]/', $tracker['date']);
            $this->assertRegExp('/[a-zA-Z0-9 ]/', $tracker['location']);
            $this->assertRegExp('/[a-zA-Z0-9 ]/', $tracker['description']);
            $this->assertRegExp('/[a-zA-Z0-9 ]?/', $tracker['status']);
        }
    }
    
    public function testCleanText() {

        $input = 'เสาร์ 16 กุมภาพันธ์ 2562 <br> 11:30:28 น.';
        $expected = 'เสาร์ 16 กุมภาพันธ์ 2562  11:30:28 น.';
        $result = $this->Tracker->cleanText($input);
        $this->assertEquals($expected, $result);

        $input = '
        ผู้รับได้รับสิ่งของเรียบร้อยแล้ว         
        ';
        $expected = 'ผู้รับได้รับสิ่งของเรียบร้อยแล้ว';
        $result = $this->Tracker->cleanText($input);
        $this->assertEquals($expected, $result);

    }

    public function testParseHTML() {

        $html = file_get_contents(__DIR__ .'/fixtures/1.html');
        $result = $this->Tracker->parseHTML($html);
        $this->assertEquals(11, count($result));

    }

}
