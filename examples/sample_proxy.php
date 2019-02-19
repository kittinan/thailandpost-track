<?php

require_once __DIR__ .'/../vendor/autoload.php';
require_once __DIR__ . '/../src/KS/THAILANDPOST/Track.php';

$proxy = 'socks5://localhost:9050';
$proxy = '1.2.169.34:8080';

$track = new \KS\THAILANDPOST\Track('chromium-browser', $proxy);

$ems = 'EW650052642TH';
$trackers = $track->getTracks($ems);
print_r($trackers);