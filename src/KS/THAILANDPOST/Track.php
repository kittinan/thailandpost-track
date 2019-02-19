<?php

namespace KS\THAILANDPOST;

use HeadlessChromium\BrowserFactory;
use PHPHtmlParser\Dom;
use Exception;

class RequestRejectException extends Exception { }

class Track {

    public static $URL_POST = 'http://track.thailandpost.co.th/tracking/default.aspx?lang=';

    private $url = '';
    private $browser;

    public function __construct($chrome_bin_path='chromium-browser', $proxy=null) {
        $this->enableThaiLanguage();

        $browserFactory = new BrowserFactory($chrome_bin_path);
        $option = [
            'windowSize' => [1280, 800],
            'headless' => true,
        ];

        if (!empty($proxy)) {
            $option['customFlags'] = [
                '--proxy-server="' . $proxy . '"', 
                //'--incognito',
            ];
        }

        $this->browser = $browserFactory->createBrowser($option);
    }

    public function __destruct() {
        $this->browser->close();
    }
    
    public function enableThaiLanguage() {
        $this->url = Track::$URL_POST . 'th';
    }
    public function enableEngLanguage() {
        $this->url = Track::$URL_POST . 'en';
    }

    public function getTracks($trackerNumber) {
        if (empty($trackerNumber) || strlen($trackerNumber) != 13) {
            return false;
        }

        $trackerNumber = strtoupper($trackerNumber);
        
        $page = $this->browser->createPage();
        $page->navigate($this->url)->waitForNavigation('networkIdle', 10000);

        $evaluation = $page->evaluate(
            '(() => {
                    document.querySelector("#TextBarcode").value = "' . $trackerNumber . '";
                })()'
            );
          
        try {
            $slider = $page->evaluate("$('.bgSlider').position()")->getReturnValue();
        } catch (Exception $e) {
            throw new RequestRejectException('Thailand post ban your ip address');
        }
        

        // Mouse slide
        $page->mouse()
        ->move($slider['left'] + 5, $slider['top'] + 5)       
        ->press()                       
        ->move($slider['left'] + 5 + 179,  $slider['top'] + 5, ['steps' => 1])      
        ->release();
        
        // wait the page load
        $page->waitForReload();

        $html = $page->evaluate("document.querySelector('body').innerHTML")->getReturnValue();

        //$page->close();

        $tracks = $this->parseHTML($html);
        
        return !empty($tracks) ? $tracks : false;
    }

    public function _getTracks($trackerNumber) {
        if (empty($trackerNumber) || strlen($trackerNumber) != 13) {
            return false;
        }

        $trackerNumber = strtoupper($trackerNumber);

        $browserFactory = new BrowserFactory('/usr/bin/chromium-browser');
        $browser = $browserFactory->createBrowser([
            'windowSize' => [1280, 800],
            'headless' => true,
          ]);

        $page = $browser->createPage();
        $page->navigate($this->url)->waitForNavigation('networkIdle', 10000);

        $evaluation = $page->evaluate(
            '(() => {
                    document.querySelector("#TextBarcode").value = "' . $trackerNumber . '";
                })()'
            );
          
        $slider = $page->evaluate("$('.bgSlider').position()")->getReturnValue();

        $page->mouse()
        ->move($slider['left'] + 5, $slider['top'] + 5)       
        ->press()                       
        ->move($slider['left'] + 5 + 179,  $slider['top'] + 5, ['steps' => 1])      
        ->release();
        
        // given the last click was on a link, the next step will wait for the page to load after the link was clicked
        $page->waitForReload();

        $html = $page->evaluate("document.querySelector('body').innerHTML")->getReturnValue();

        $browser->close();
        
        return $this->parseHTML($html);
    }

    public function parseHTML($html) {
        //Convert TIS-620 to UTF-8
        //$html = iconv('TIS-620', 'UTF-8//IGNORE', $html);
        
        $dom = new Dom();
        $dom->load($html, ['enforceEncoding' => 'UTF-8']);
        $trs = @$dom->find('table#DataGrid1 tr');

        $results = [];
        for ($i = 1; $i < count($trs); $i++) {
            $row = $trs[$i];
            $tds = $row->find('td');

            $result = [];
            $result['date'] = $this->cleanText($tds[0]);
            $result['location'] = $this->cleanText($tds[1]);
            $result['description'] = $this->cleanText($tds[2]);
            $result['status'] = $this->cleanText($tds[3]);

            $results[] = $result;
        }

        return $results;
    }
    
    public function cleanText($str) {
        return trim(str_replace('&nbsp;', '', strip_tags($str)));
    }
    
}
