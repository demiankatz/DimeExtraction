<?php

require_once 'vendor/autoload.php';

TextRazorSettings::setApiKey(file_get_contents('textrazor.key'));

$text = file_get_contents($argv[1]);

if (!$text) {
  die('no text');
}

$modes = ['entities', 'topics', 'words', 'phrases', 'dependency-trees', 'relations', /*'entailments', */'senses'];
$chunks = getchunks($text);

if (!file_exists(__DIR__ . '/textrazor-out')) {
    mkdir(__DIR__ . '/textrazor-out');
}

foreach ($modes as $mode) {
    echo "running $mode on " . count($chunks) . " chunks...\n";
    $tr = new TextRazor();
    $tr->addExtractor($mode);

	foreach ($chunks as $i => $chunk) {
		try {
			$ent = $tr->analyze($chunk);
		} catch (Exception $e) {
			echo "Exception: " . $e->getMessage() . "\n";
			exit;
		}
		file_put_contents(__DIR__ . '/textrazor-out/' . $mode . '-' . $i . '-' . basename($argv[1]), serialize($ent));
	}
}

function getchunks($text, $chunkSize = 200000)
{
	$chunks = [];
	while (strlen($text) > $chunkSize) {
	    $optimalChunkNumber = ceil(strlen($text) / $chunkSize);
		$optimalChunkSize = ceil(strlen($text) / $optimalChunkNumber);
		$chunk = getNextChunk($text, $optimalChunkSize);
		$text = trim(substr($text, strlen($chunk)));
		$chunks[] = trim($chunk);
	}
	if (!empty($text)) {
		$chunks[] = trim($text);
	}
	return $chunks;
}

function getNextChunk($text, $chunkSize)
{
	static $breakingChars = [' ', "\n"];

	while (!in_array(substr($text, $chunkSize, 1), $breakingChars) && $chunkSize > 0) {
		$chunkSize--;
	}
	if ($chunkSize < 1) {
		throw new Exception('Chunking went wrong!');
	}
	return substr($text, 0, $chunkSize);
}
