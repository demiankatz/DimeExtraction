<?php

$aiHandle = fopen($argv[1], 'r');
$slHandle = fopen($argv[2], 'r');
$trHandle = fopen($argv[3], 'r');
$output = fopen($argv[4], 'w');
if (!$aiHandle || !$trHandle || !$output) {
    die("Problem opening file(s)");
}
$aiHeader = prefixHeader('AlchemyAPI ', fgetcsv($aiHandle));
$slHeader = prefixHeader('Spotlight ', fgetcsv($slHandle));
$trHeader = prefixHeader('TextRazor ', fgetcsv($trHandle));
$aiDummies = generateDummyRow(count($aiHeader));
$slDummies = generateDummyRow(count($slHeader));
$trDummies = generateDummyRow(count($trHeader));
fputcsv($output, array_merge(['Matched Rows', 'Matched IDs'], $aiHeader, $slHeader, $trHeader));

/* AlchemyAPI indexing routine -- no longer used, since we're now making this the center of the join
$aiRows = $aiFreebaseIndex = $aiKeywordIndex = [];
while ($line = fgetcsv($aiHandle)) {
    $aiRows[] = $line;
    if (isset($aiKeywordIndex[strtolower($line[0])])) {
        echo "Warning: duplicate AlchemyAPI row for {$line[0]}\n";
    }
    $aiKeywordIndex[strtolower($line[0])] = & $aiRows[count($aiRows) - 1];
    if (!empty($line[8])) {
        $aiFreebaseIndex[$line[8]] = & $aiRows[count($aiRows) - 1];
    }
}
fclose($aiHandle);
*/

$trRows = $trFreebaseIndex = $trKeywordIndex = [];
while ($line = fgetcsv($trHandle)) {
    $trRows[] = $line;
    if (isset($trKeywordIndex[strtolower($line[0])])) {
        echo "Warning: duplicate AlchemyAPI row for {$line[0]}\n";
    }
    $trKeywordIndex[strtolower($line[0])] = & $trRows[count($trRows) - 1];
    if (!empty($line[3])) {
        $fbId = 'http://rdf.freebase.com/ns' . $line[3];
        $trFreebaseIndex[$fbId] = & $trRows[count($trRows) - 1];
    }
}
fclose($trHandle);

$slRows = $slDbpediaIndex = $slKeywordIndex = [];
while ($line = fgetcsv($slHandle)) {
    $slRows[] = $line;
    $slDbpediaIndex[$line[0]] = & $slRows[count($slRows) - 1];
    foreach (array_unique(array_map('strtolower', explode(',', $line[3]))) as $keyword) {
        if (!empty($keyword)) {
            if (isset($slKeywordIndex[$keyword])) {
                echo "Warning: duplicate Spotlight row for {$keyword}\n";
            }
            $slKeywordIndex[$keyword] = & $slRows[count($slRows) - 1];
        }
    }
}
fclose($slHandle);

// first try to find matches around AlchemyAPI, which has the potential for a three-way join
$slMatchedIds = $trMatchedKeywords = [];
while ($line = fgetcsv($aiHandle)) {
    unset($trMatchedLine);
    unset($slMatchedLine);
    $matchedRows = $matchedIds = 0;
    if (!empty($line[8])) {
        if (isset($trFreebaseIndex[$line[8]])) {
            $matchedIds++;
            $trMatchedLine = & $trFreebaseIndex[$line[8]];
        }
    }
    if (!isset($trMatchedLine) && isset($trKeywordIndex[strtolower($line[0])])) {
        $trMatchedLine = & $trKeywordIndex[strtolower($line[0])];
    }
    if (!isset($trMatchedLine)) {
        $trMatchedLine = & $trDummies;
    } else {
        $matchedRows++;
        $trMatchedKeywords[] = $trMatchedLine[0];
    }
    if (!is_array($trMatchedLine)) {
        var_dump($trMatchedLine);
        die('Unexpected value');
    }
 
    if (!empty($line[7])) {
        if (isset($slDbpediaIndex[$line[7]])) {
            $matchedIds++;
            $slMatchedLine = & $slDbpediaIndex[$line[7]];
        }
    }
    if (!isset($slMatchedLine) && isset($slKeywordIndex[strtolower($line[0])])) {
        $slMatchedLine = & $slKeywordIndex[strtolower($line[0])];
    }
    if (!isset($slMatchedLine)) {
        $slMatchedLine = & $slDummies;
    } else {
        $matchedRows++;
        $slMatchedIds[] = $slMatchedLine[0];
    }
    
    fputcsv($output, array_merge([$matchedRows, $matchedIds], $line, $slMatchedLine, $trMatchedLine));
}
fclose($aiHandle);

// check unmatched TextRazor rows to see if we can join them to Spotlight.
foreach ($trRows as $line) {
    // already stored this line? skip it!
    if (in_array($line[0], $trMatchedKeywords)) {
        continue;
    }
    unset($slMatchedLine);
    $matchedRows = $matchedIds = 0;
    if (!empty($line[2])) {
        $parts = explode('/', $line[2]);
        $lastPart = array_pop($parts);
        $dbpediaId = 'http://dbpedia.org/resource/' . $lastPart;
        if (isset($slDbpediaIndex[$dbpediaId])) {
            $matchedIds++;
            $slMatchedLine = & $slDbpediaIndex[$dbpediaId];
        }
    }
    if (!isset($slMatchedLine) && isset($slKeywordIndex[strtolower($line[0])])) {
        $slMatchedLine = & $slKeywordIndex[strtolower($line[0])];
    }
    if (!isset($slMatchedLine)) {
        $slMatchedLine = & $slDummies;
    } else {
        $matchedRows++;
        $slMatchedIds[] = $slMatchedLine[0];
    }
    fputcsv($output, array_merge([$matchedRows, $matchedIds], $aiDummies, $slMatchedLine, $line));
}

// finally, dump out unmatched Spotlight rows.
foreach ($slRows as $line) {
    if (!in_array($line[0], $slMatchedIds)) {
        fputcsv($output, array_merge([1, 0], $aiDummies, $line, $trDummies));
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