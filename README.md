thailandpost-track
========
[![Build Status](https://travis-ci.org/kittinan/thailandpost-track.svg?branch=master)](https://travis-ci.org/kittinan/thailandpost-track)
[![Code Coverage](https://scrutinizer-ci.com/g/kittinan/php-http/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/kittinan/thailandpost-track/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kittinan/php-http/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kittinan/thailandpost-track/?branch=master)
[![License](https://poser.pugx.org/kittinan/thailandpost-track/license)](https://packagist.org/packages/kittinan/thailandpost-track)
[![Latest Stable Version](https://poser.pugx.org/kittinan/thailandpost-track/v/stable)](https://packagist.org/packages/kittinan/thailandpost-track)

Simple library for thailandpost track EMS

## Requirement
* PHP 5.3+
* php5-curl
* [kittinan/php-http](https://github.com/kittinan/php-http)

## Composer

Install the latest version with composer
```
composer require kittinan/thailandpost-track
```

This library on the Packagist.

[https://packagist.org/packages/kittinan/thailandpost-track](https://packagist.org/packages/kittinan/thailandpost-track)

## Usage
```php
//Do not forget to define cookie path in constrauctor
$track = new \KS\THAILANDPOST\Track('/tmp/cookie.txt');
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
//Do not forget to define cookie path in constrauctor
$track = new \KS\THAILANDPOST\Track('/tmp/cookie.txt');

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
