<?php

$aiHandle = fopen($argv[1], 'r');
$trHandle = fopen($argv[2], 'r');
$output = fopen($argv[3], 'w');
if (!$aiHandle || !$trHandle || !$output) {
    die("Problem opening file(s)");
}
$aiHeader = prefixHeader('AlchemyAPI ', fgetcsv($aiHandle));
$trHeader = prefixHeader('TextRazor ', fgetcsv($trHandle));
$aiDummies = generateDummyRow(count($aiHeader));
$trDummies = generateDummyRow(count($trHeader));
fputcsv($output, array_merge($aiHeader, $trHeader));

$aiRows = $freebaseIndex = $keywordIndex = [];
while ($line = fgetcsv($aiHandle)) {
    $aiRows[] = $line;
    if (isset($keywordIndex[strtolower($line[0])])) {
        echo "Warning: duplicate row for {$line[0]}\n";
    }
    $keywordIndex[strtolower($line[0])] = & $aiRows[count($aiRows) - 1];
    if (!empty($line[8])) {
        $freebaseIndex[$line[8]] = & $aiRows[count($aiRows) - 1];
    }
}
fclose($aiHandle);

$matchedKeywords = [];
while ($line = fgetcsv($trHandle)) {
    unset($matchedLine);
    if (!empty($line[3])) {
        $fbId = 'http://rdf.freebase.com/ns' . $line[3];
        if (isset($freebaseIndex[$fbId])) {
            $matchedLine = & $freebaseIndex[$fbId];
        }
    }
    if (!isset($matchedLine) && isset($keywordIndex[strtolower($line[0])])) {
        $matchedLine = & $keywordIndex[strtolower($line[0])];
    }
    if (!isset($matchedLine)) {
        $matchedLine = & $aiDummies;
    } else {
        $matchedKeywords[] = $matchedLine[0];
    }
    if (!is_array($matchedLine)) {
        var_dump($matchedLine);
        die('boom');
    }
    fputcsv($output, array_merge($matchedLine, $line));
}
fclose($trHandle);

foreach ($aiRows as $line) {
    if (!in_array($line[0], $matchedKeywords)) {
        fputcsv($output, array_merge($line, $trDummies));
    }
}
fclose($output);

function prefixHeader($prefix, $row)
{
    $result = [];
    foreach ($row as $current) {
        $result[] = $prefix . $current;
    }
    return $result;
}

function generateDummyRow($c, $text = 'n/a')
{
    $row = [];
    for ($i = 0; $i < $c; $i++) {
        $row[] = $text;
    }
    return $row;
}