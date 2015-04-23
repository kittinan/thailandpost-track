<?php

namespace KS\THAILANDPOST;

class Track {

    public static $URL_POST = 'http://track.thailandpost.co.th/tracking/default.aspx?lang=';
    public static $URL_QAPTCHA = 'http://track.thailandpost.co.th/tracking/Server.aspx';
    
    private $userAgent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0';
    private $url = '';
    private $Http = null;

    public function __construct($cookiePath) {
        $this->Http = new \KS\HTTP\HTTP($cookiePath);
        $this->Http->setUserAgent($this->userAgent);
        $this->enableThaiLanguage();
    }
    
    public function enableThaiLanguage() {
        $this->url = Track::$URL_POST . 'th';
    }
    public function enableEngLanguage() {
        $this->url = Track::$URL_POST . 'end';
    }

    public function getTracks($trackerNumber) {
        if (empty($trackerNumber) || strlen($trackerNumber) != 13) {
            return false;
        }

        $trackerNumber = strtoupper($trackerNumber);
        
        //Find require parameter
        $html = $this->Http->get($this->url);
        if (empty($html)) {
            return false;
        }
        
        $pattern = '/<input type="hidden" name="(.*?)".*?value="(.*?)"/';
        preg_match_all($pattern, $html, $matches);
        if (empty($matches[1]) || count($matches[1]) < 4) {
            return false;
        }
        
        $QapTchaName = $this->generatePass(32);
        $QapTchaValue  = $this->generatePass(7);
        
        //Bypass QapTcha
        $paramsQap = array();
        $paramsQap['action'] = 'qaptcha';
        $paramsQap['qaptcha_key'] = $QapTchaName;
        $this->Http->post(Track::$URL_QAPTCHA, $paramsQap);
        
        
        $params = array();
        foreach ($matches[1] as $index => $key) {
            $params[$key] = $matches[2][$index];
        }
        
        $params['TextBarcode'] = $trackerNumber;
        $params[$QapTchaValue] = '';
        $params['CaptchaCTL1$submit'] = 'Submit Query';
        $params['textkey'] = $this->generateTextKey();
        
        $html = $this->Http->post($this->url, $params);
        if (empty($html)) {
            return false;
        }
        
        //Convert TIS-620 to UTF-8
        $html = iconv('TIS-620', 'UTF-8//IGNORE', $html);
        
        $pattern = '#<tr.*?>[\s\S]*?<td.*?>(.*?)</td><td.*?>(.*?)</td><td.*?>(.*?)</td><td.*?>(.*?)</td>#';
        preg_match_all($pattern, $html, $matches);
        if (empty($matches[1])) {
            return false;
        }
        
        //Format result
        $result = array();
        foreach ($matches[1] as $index => $date) {
            $date = strip_tags($date);
            
            $row = array();
            $row['date'] = $this->cleanText($date);
            $row['location'] = $this->cleanText($matches[2][$index]);
            $row['description'] = $this->cleanText($matches[3][$index]);
            $row['status'] = $this->cleanText($matches[4][$index]);
            array_push($result, $row);
        }
        
        return $result;
    }

    private function generatePass($nb) {
        $chars = 'azertyupqsdfghjkmwxcvbn23456789AZERTYUPQSDFGHJKMWXCVBN_-#@';
        $pass = '';
        $char_length = strlen($chars) - 1;
        for($i = 0; $i < $nb; $i++) {
            $wpos = (int) round(rand(0, $char_length));
            $pass .= $chars[$wpos];
        }
        return $pass;
    }
    
    private function generateTextKey() {
        $textKey = '';
        
        $start = rand(1, 15);
        $end = rand(140, 160);
        $y = rand(2, 37);
        
        for ($i = $start; $i <= $end; $i++) {
            $textKey .= '[' . $i . ',' . $y .'],';
        }
        
        return $textKey;
    }
    
    private function cleanText($str) {
        return str_replace('&nbsp;', '', strip_tags($str));
    }
    
}
