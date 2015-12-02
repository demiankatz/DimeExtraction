<?php

require_once 'vendor/autoload.php';

TextRazorSettings::setApiKey(file_get_contents('textrazor.key'));

$text = file_get_contents($argv[1]);

if (!$text) {
  die('no text');
}

$modes = ['entities', 'topics', 'words', 'phrases', 'dependency-trees', 'relations', /*'entailments', */'senses'];
$chunks = \DimeExtraction\Chunker::getChunks($text);

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