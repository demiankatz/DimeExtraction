<?php

require_once 'vendor/autoload.php';

$text = file_get_contents($argv[1]);
$chunkSize = (isset($argv[2]) && $argv[2] > 0)
    ? intval($argv[2]) : -1;

if (!$text) {
    die('no text');
}

$api = new AlchemyAPI();

$dir = __DIR__ . '/alchemyapi-out/';
if (!file_exists($dir)) {
    mkdir($dir);
}

$chunks = $chunkSize > 0
    ? \DimeExtraction\Chunker::getChunks($text, $chunkSize) : [$text];

foreach ($chunks as $i => $text) {
    $suffix = basename(substr($argv[1], 0, strlen($argv[1]) - 4)) . ($i > 0 ? '-' . ($i + 1) : '') . '.txt';

    $ent = $api->entities('text', $text, ['maxRetrieve' => 1000]);
    file_put_contents($dir . 'entities-' . $suffix, serialize($ent));

    $key = $api->keywords('text', $text, ['maxRetrieve' => 1000]);
    file_put_contents($dir . 'keywords-' . $suffix, serialize($key));

    $con = $api->concepts('text', $text, ['maxRetrieve' => 1000]);
    file_put_contents($dir . 'concepts-' . $suffix, serialize($con));

    $sent = $api->sentiment('text', $text, []);
    file_put_contents($dir . 'sentiment-' . $suffix, serialize($sent));
}
