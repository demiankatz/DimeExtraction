<?php

require_once 'vendor/autoload.php';

$text = file_get_contents($argv[1]);

if (!$text) {
  die('no text');
}

$api = new AlchemyAPI();

$dir = __DIR__ . '/alchemyapi-out/';
if (!file_exists($dir)) {
    mkdir($dir);
}

$ent = $api->entities('text', $text, ['maxRetrieve' => 1000]);
file_put_contents($dir . 'entities-' . basename($argv[1]), serialize($ent));

$key = $api->keywords('text', $text, ['maxRetrieve' => 1000]);
file_put_contents($dir . 'keywords-' . basename($argv[1]), serialize($key));

$con = $api->concepts('text', $text, ['maxRetrieve' => 1000]);
file_put_contents($dir . 'concepts-' . basename($argv[1]), serialize($con));

$sent = $api->sentiment('text', $text, []);
file_put_contents($dir . 'sentiment-' . basename($argv[1]), serialize($sent));
