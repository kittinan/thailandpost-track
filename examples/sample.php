<?php

require_once __DIR__ .'/../vendor/autoload.php';
require_once __DIR__ . '/../src/KS/THAILANDPOST/Track.php';

$track = new \KS\THAILANDPOST\Track('chromium-browser');

$ems = 'EW650052642TH';
$trackers = $track->getTracks($ems);
print_r($trackers);