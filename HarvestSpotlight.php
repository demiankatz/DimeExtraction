<?php

$text = file_get_contents($argv[1]);

if (!$text) {
    die('no text');
}

$dir = __DIR__ . '/spotlight-out/';
if (!file_exists($dir)) {
    mkdir($dir);
}
$outfile = $dir . str_replace('.txt', '.json', basename($argv[1]));
$fp = fopen($outfile, 'w');

$ch = curl_init('http://spotlight.dbpedia.org/rest/annotate/');
curl_setopt($ch, CURLOPT_POSTFIELDS, 'text=' . urlencode($text));
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept:application/json', 'content-type:application/x-www-form-urlencoded']);
curl_exec($ch);
curl_close($ch);

fclose($fp);
