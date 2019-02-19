<?php

require_once 'vendor/autoload.php';
require_once __DIR__ . '/src/KS/THAILANDPOST/Track.php';

$t = new \KS\THAILANDPOST\Track('/tmp/cookie.txt');
$t->enableEngLanguage();

$ems = 'EW650052642TH';
$trackers = $t->getTracks($ems);
var_dump($trackers);