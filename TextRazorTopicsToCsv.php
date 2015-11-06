<?php

$handle = fopen(str_replace('.txt', '.csv', $argv[1]), 'w');
fputcsv($handle, ['Label', 'Wiki Link', 'Score', 'Section']);

for ($i = 1; $i < count($argv); $i++) {
	$data = unserialize(file_get_contents($argv[$i]));
	foreach (['coarseTopics', 'topics'] as $section) {
		foreach ($data['response'][$section] as $current) {
			$arr = [$current['label'], $current['wikiLink'], $current['score'], $section];
			fputcsv($handle, $arr);
		}
	}
}

fclose($handle);
