<?php

require_once __DIR__ .'/../vendor/autoload.php';
require_once __DIR__ . '/../src/KS/THAILANDPOST/Track.php';

$track = new \KS\THAILANDPOST\Track('chromium-browser');
$track->setTimeout(30000); //Set timeout for 30 seconds

$ems = 'EW650052642TH';
$trackers = $track->getTracks($ems);
print_r($trackers);