<?php

$handle = fopen(str_replace('.txt', '.csv', $argv[1]), 'w');
$header = ['text', 'type', 'relevance', 'count'];
$disambiguatedFields = ['subType', 'name', 'geo', 'dbpedia', 'freebase', 'census', 'yago', 'website', 'ciaFactbook', 'opencyc', 'geonames', 'crunchbase', 'musicBrainz'];
fputcsv($handle, array_merge($header, $disambiguatedFields));

for ($i = 1; $i < count($argv); $i++) {
	$data = unserialize(file_get_contents($argv[$i]));
	foreach ($data['entities'] as $current) {
		$arr = [$current['text'], $current['type'], $current['relevance'], $current['count']];
		foreach ($disambiguatedFields as $field) {
		    $arr[] = isset($current['disambiguated'][$field]) ? implode(', ', (array)$current['disambiguated'][$field]) : '';
		}
		if (isset($current['disambiguated'])) {
			$keys = array_keys($current['disambiguated']);
			foreach ($keys as $key) {
				if (!in_array($key, $disambiguatedFields)) {
					echo "Unexpected key: $key\n";
				}
			}
		}
		fputcsv($handle, $arr);
	}
}

fclose($handle);