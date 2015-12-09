<?php

$outfile = str_replace('.txt', '.csv', $argv[1]);
$outfile = preg_replace('/-[0-9]+/', '', $outfile);
$handle = fopen($outfile, 'w');
fputcsv($handle, ['Entity Id', 'Matched Text', 'Wiki Link', 'Freebase ID', 'Confidence Score', 'Relevance Score']);

for ($i = 1; $i < count($argv); $i++) {
	$data = unserialize(file_get_contents($argv[$i]));
	foreach ($data['response']['entities'] as $current) {
		$arr = [$current['entityId'], $current['matchedText'], $current['wikiLink'], isset($current['freebaseId']) ? $current['freebaseId'] : null, $current['confidenceScore'], $current['relevanceScore']];
		fputcsv($handle, $arr);
	}
}

fclose($handle);
