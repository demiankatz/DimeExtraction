<?php
namespace DimeExtraction;

class Chunker
{
    public function getChunks($text, $chunkSize = 200000)
    {
        $chunks = [];
        while (strlen($text) > $chunkSize) {
            $optimalChunkNumber = ceil(strlen($text) / $chunkSize);
            $optimalChunkSize = ceil(strlen($text) / $optimalChunkNumber);
            $chunk = static::getNextChunk($text, $optimalChunkSize);
            $text = trim(substr($text, strlen($chunk)));
            $chunks[] = trim($chunk);
        }
        if (!empty($text)) {
            $chunks[] = trim($text);
        }
        return $chunks;
    }

    protected function getNextChunk($text, $chunkSize)
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
}