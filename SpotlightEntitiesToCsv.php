<?php

$outfile = str_replace('.json', '.csv', $argv[1]);
$outfile = preg_replace('/-[0-9]+/', '', $outfile);
$handle = fopen($outfile, 'w');
fputcsv($handle, ['URI', 'support', 'types', 'surface form', 'offset', 'similarity score', 'percentage of second rank']);
for ($i = 1; $i < count($argv); $i++) {
    $data = json_decode(file_get_contents($argv[$i]));
    foreach ($data->Resources as $current) {
        $arr = [$current->{'@URI'}, $current->{'@support'}, $current->{'@types'}, $current->{'@surfaceForm'}, $current->{'@offset'}, $current->{'@similarityScore'}, $current->{'@percentageOfSecondRank'}];
        fputcsv($handle, $arr);
    }
}

fclose($handle);
