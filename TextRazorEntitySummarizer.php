<?php

$handle = fopen($argv[1], 'r');

$header = fgetcsv($handle);
$header[] = 'Count';
$header[] = 'Variant Forms';

$data = [];
while ($line = fgetcsv($handle)) {
    $id = strtolower($line[0]);
	if (!isset($data[$id])) {
	    $data[$id] = [$line];
	} else {
		$data[$id][] = $line;
	}
}
fclose($handle);

$handle = fopen(dirname($argv[1]) . '/' . 'summary-' . basename($argv[1]), 'w');
fputcsv($handle, $header);

foreach ($data as $group) {
	$count = $relevance = $confidence = 0;
	$forms = $lastLine = $matchedTexts = [];
	foreach ($group as $line) {
	    if ($count > 0) {
		    for ($i = 2; $i < 4; $i++) {
				if (empty($line[$i]) && !empty($lastLine[$i])) {
					$line[$i] = $lastLine[$i];
				} elseif ($lastLine[$i] != $line[$i] && !empty($lastLine[$i])) {
				    echo "Warning: mismatch in position $i for " . $line[0] . "\n";
				}
			}
		}
		$count++;
        $forms[] = $line[0];
		$matchedTexts[] = $line[1];
		$confidence += $line[4];
		$relevance += $line[5];
		$lastLine = $line;
	}
    $line[0] = pickBestForm($forms);
	$line[1] = implode(', ', array_unique($matchedTexts));
	$line[4] = $confidence / $count;
	$line[5] = $relevance / $count;
	$line[] = $count;
    $line[] = implode(', ', array_unique($forms));
	fputcsv($handle, $line);
}

fclose($handle);

function pickBestForm($forms)
{
    $upper = $lower = $mixed = false;
    foreach ($forms as $form) {
        if (strtoupper($form) === $form) {
            $upper = $form;
        } else if (strtolower($form) === $form) {
            $lower = $form;
        } else {
            // mixed is most preferred
            return $form;
        }
    }
    if ($lower) {
        // lower is second best
        return $lower;
    }
    // upper is last resort
    return $upper;
}