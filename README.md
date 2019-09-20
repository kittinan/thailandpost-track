# ![#f03c15](https://placehold.it/15/f03c15/000000?text=+)  Not working (2019-09-20)  ![#f03c15](https://placehold.it/15/f03c15/000000?text=+) 

Thailand POST change their web page and open the API https://track.thailandpost.co.th/developerGuide

[Issue #3](https://github.com/kittinan/thailandpost-track/issues/3)

thailandpost-track
========
[![Build Status](https://travis-ci.org/kittinan/thailandpost-track.svg?branch=master)](https://travis-ci.org/kittinan/thailandpost-track)
[![License](https://poser.pugx.org/kittinan/thailandpost-track/license)](https://packagist.org/packages/kittinan/thailandpost-track)
[![Latest Stable Version](https://poser.pugx.org/kittinan/thailandpost-track/v/stable)](https://packagist.org/packages/kittinan/thailandpost-track)

Simple library for thailandpost track EMS with Chrome headless

## Requirement
* PHP 7.0+
* mbstring extension
* Chrome binary

## Composer

Install the latest version with composer
```
composer require kittinan/thailandpost-track
```

This library on the Packagist.

[https://packagist.org/packages/kittinan/thailandpost-track](https://packagist.org/packages/kittinan/thailandpost-track)

## Install Chrome

Install chromium-browser or Google Chrome

### Ubuntu/Debian

```
sudo apt-get install chromium-browser
```

## Usage
```php
//Do not forget to define Google Chrome or Chromium binary path

$chrome_bin = '/usr/bin/chromium-browser';
$track = new \KS\THAILANDPOST\Track($chrome_bin);
$ems = 'EN123456789TH';
$trackers = $track->getTracks($ems);

//Result return false or array of track status
Array
(
    [0] => Array
        (
            [date] => April 17, 2015  10:42:25
            [location] => PAK CHONG
            [description] => Accept
            [status] => 
        )

    [1] => Array
        (
            [date] => April 17, 2015  13:42:39
            [location] => PAK CHONG
            [description] => Items Into Container
            [status] => 
        )
)

```

You can enable English language.
```php
//Do not forget to define Google Chrome or Chromium binary path

$chrome_bin = '/usr/bin/chromium-browser';
$track = new \KS\THAILANDPOST\Track($chrome_bin);

//For Thai language (default)
$track->enableThaiLanguage

//For English language
$track->enableEngLanguage();

$ems = 'EN123456789TH';
$trackers = $track->getTracks($ems);

```


License
=======
The MIT License (MIT)
