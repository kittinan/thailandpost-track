<?php

namespace KS\THAILANDPOST;

use HeadlessChromium\BrowserFactory;
use PHPHtmlParser\Dom;
use Exception;

class RequestRejectException extends Exception { }

class Track {

    public static $URL_POST = 'http://track.thailandpost.co.th/tracking/default.aspx?';

    private $url = '';
    private $browser;
    private $timeout = 10000;

    public function __construct($chrome_bin_path ='chromium-browser', $lang = 'th', $proxy = null) {
        if ($lang == 'th') {
            $this->enableThaiLanguage(); 
        } else {
            $this->enableEngLanguage(); 
        }
        
        $browserFactory = new BrowserFactory($chrome_bin_path);
        $options = [
            'windowSize' => [1280, 800],
            'headless' => true,
        ];
        
        if (!empty($proxy)) {
            $options['customFlags'] = [
                '--proxy-server="' . $proxy . '"', 
                //'--incognito',
            ];
        }
        
        $this->browser = $browserFactory->createBrowser($options);
    }

    public function __destruct() {
        $this->browser->close();
    }

    public function setTimeout($timeout) {
        $this->timeout = $timeout;
    }
    
    public function enableThaiLanguage() {
        $this->url = Track::$URL_POST . 'lang=th';
    }
    public function enableEngLanguage() {
        $this->url = Track::$URL_POST . 'lang=en';
    }

    public function getTracks($trackerNumber) {
        if (empty($trackerNumber) || strlen($trackerNumber) != 13) {
            return false;
        }

        $trackerNumber = strtoupper($trackerNumber);

        $page = $this->browser->createPage();
        $page->navigate($this->url)->waitForNavigation('networkIdle', $this->timeout);

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

    public function parseHTML($html) {
        //Convert TIS-620 to UTF-8
        //$html = iconv('TIS-620', 'UTF-8//IGNORE', $html);
        
        $dom = new Dom();
        $dom->load($html, ['enforceEncoding' => 'UTF-8']);
        $trs = $dom->find('table#DataGrid1 tr');

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
