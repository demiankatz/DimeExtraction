<?php

$handle = fopen($argv[1], 'r');

$header = fgetcsv($handle);
$header[] = 'Count';

$colsToDiff = [1, 2, 6];
$data = [];
while ($line = fgetcsv($handle)) {
    $id = $line[0];
	if (!isset($data[$id])) {
        $line[] = 1; // initialize count
	    $data[$id] = $line;
	} else {
        foreach ($colsToDiff as $col) {
            if ($data[$id][$col] != $line[$col]) {
                die("Unexpected mismatch in column $col: " . print_r($data[$id], true) . print_r($line, true));
            }
        }
        // merge surface forms:
        $forms = explode(',', $data[$id][3]);
        $forms[] = $line[3];
        $data[$id][3] = implode(',', array_unique($forms));
        $data[$id][4] .= ',' . $line[4];    // append offsets
        // if similarity score doesn't match, create an average:
        if ($data[$id][5] != $line[5]) {
            $data[$id][5] = (($data[$id][5] * $data[$id][7]) + $line[5]) / ($data[$id][7] + 1);
        }
        $data[$id][7]++;                    // increment count
	}
}
fclose($handle);

$handle = fopen(dirname($argv[1]) . '/' . 'summary-' . basename($argv[1]), 'w');
fputcsv($handle, $header);
foreach ($data as $current) {
    fputcsv($handle, $current);
}
fclose($handle);