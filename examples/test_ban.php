<?php

require_once __DIR__ .'/../vendor/autoload.php';
require_once __DIR__ . '/../src/KS/THAILANDPOST/Track.php';

$track = new \KS\THAILANDPOST\Track('chromium-browser');

$tracker_ids = [
    "EW650127270TH","EW650127266TH","EW650052642TH","EW650064875TH",
    "EW650064861TH","EW650064858TH","EW650047903TH","EW650047917TH",
    "EW649816405TH","EW649865471TH","EW649865468TH","EW649865454TH",
    "EW437919512TH","EW437916065TH","EW437916105TH","EW437916096TH",
    "EW437916079TH","EW437916082TH","EW437916051TH","EW437953981TH",
    "EW437953978TH","EW438334888TH","EW438334874TH","EW438334865TH",
    "EW649971869TH","EW649971855TH","EW649971841TH","EW649971838TH",
    "EW649927131TH","EW649927145TH","EW438095264TH","EW438095255TH",
    "EW172370564TH","EW172370547TH","EW649764115TH","EW649764138TH",
    "EW649764124TH","EW650721157TH","EW650721174TH","EW650721165TH",
    "EW649686400TH","EW649686395TH","EW649686387TH","EW650714377TH",
    "EW650714363TH","EW650705392TH","EW650705389TH","EW650705375TH",
    "EW650699520TH"
];

$time_start = microtime(true);
for ($i = 0; $i <= 999; $i++) {

    $tracker_id = $tracker_ids[array_rand($tracker_ids)];

    try {
        $trackers = $track->getTracks($tracker_id);
        echo $i . ' : ' . $tracker_id . ' | ' . count($trackers) . "\n";
    } catch (Exception $e) {
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo 'Execution time : '.$time.' seconds';
        break;
    }

    sleep(1);
}

$time_end = microtime(true);
$time = $time_end - $time_start;
echo 'Execution time : '.$time.' seconds';

