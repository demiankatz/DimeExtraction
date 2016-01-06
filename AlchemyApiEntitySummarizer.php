<?php

$handle = fopen($argv[1], 'r');

$header = fgetcsv($handle);

$data = [];
while ($line = fgetcsv($handle)) {
    $text = empty($line[5]) ? $line[0] : $line[5];
    $type = $line[1];
    $id = "$type|$text";
	if (!isset($data[$id])) {
	    $data[$id] = [$line];
	} else {
		$data[$id][] = $line;
	}
}
fclose($handle);

$handle = fopen(dirname($argv[1]) . '/' . 'summary-' . basename($argv[1]), 'w');
fputcsv($handle, $header);

foreach ($data as $key => $group) {
	$count = $relevance = 0;
	$lastLine = $matchedTexts = [];
	foreach ($group as $line) {
	    if ($count > 0) {
		    for ($i = 4; $i < count($line); $i++) {
				if (empty($line[$i]) && !empty($lastLine[$i])) {
					$line[$i] = $lastLine[$i];
				} elseif ($lastLine[$i] != $line[$i] && !empty($lastLine[$i])) {
				    echo "Warning: mismatch in position $i for " . $key . "\n";
				}
			}
		}
		$matchedTexts[] = $line[0];
        $newRelevance = $line[2];
		$newCount = $line[3];
        $relevance = ($relevance * $count) + ($newRelevance * $newCount) / ($count + $newCount);
        $count += $newCount;
		$lastLine = $line;
	}
	$line[0] = implode(', ', array_unique($matchedTexts));
	$line[2] = $relevance;
	$line[3] = $count;
	fputcsv($handle, $line);
}

fclose($handle);
