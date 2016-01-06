DimeExtraction
==============

Introduction
------------
These tools were created to experiment with entity extraction from dime novels.

Sample Files
------------
The raw-texts directory contains some dime novel texts extracted from Project Gutenberg. You can add your own raw text files there as needed.

Setting Up
----------
Run "composer install" to load dependencies.

Paste your AlchemyApi API key into api_key.txt and your TextRazor API key into textrazor.key.

Tools
-----
These tools allow retrieval of data from various APIs:
  - HarvestAlchemyApi.php [filename] [chunksize] - Dump information about [filename] into the alchemyapi-out directory. [chunksize] is an optional chunk size (maximum number of bytes) that may be used to split the text into segments, retrieving separate results for each chunk; if omitted, the entire file is sent as a single chunk.
  - HarvestSpotlight.php [filename] - Dump information about [filename] into the spotlight-out directory.
  - HarvestTextRazor.php [filename] - Dump information about [filename] into the textrazor-out directory.

These tools allow processing of the retrieved data:
  - AlchemyApiEntitiesToCsv.php [filenames] - Where [filenames] is a list of alchemyapi-out/entities*.txt files, convert them to a single CSV.
  - AlchemyApiEntitySummarizer.php [filename] - Where [filename] is an output file from AlchemyApiEntitiesToCsv.php, create a summary-*.csv file summarizing its contents.
  - CombineEntities.php [AlchemyAPI input] [Spotlight input] [TextRazor input] [output] - Where [AlchemyAPI input] is the output from AlchemyApiEntitiesToCsv.php, [Spotlight input] is the output from SpotlightEntitySummarizer.php and [TextRazor input] is the output from TextRazorEntitySummarizer.php, merge the two files together by matching identifiers and keywords, then write the result to [output].
  - DumpSerializedFile.php [filename] - Display the raw contents of [filename], where [filename] contains serialized PHP (raw AlchemyAPI and TextRazor output uses this format).
  - SpotlightEntitiesToCsv.php [filenames] - Where [filenames] is a list of spotlight-out/*.json files, convert them to a single CSV.
  - SpotlightEntitySummarizer.php [filename] - Where [filename] is an output file from SpotlightEntitiesToCsv.php, create a summary-*.csv file summarizing its contents.
  - TextRazorEntitiesToCsv.php [filenames] - Where [filenames] is a list of textrazor-out/entities*.txt files, convert them to a single CSV.
  - TextRazorEntitySummarizer.php [filename] - Where [filename] is an output file from TextRazorEntitiesToCsv.php, create a summary-*.csv file summarizing its contents.
  - TextRazorTopicsToCsv.php [filename] - Where [filename] is a textrazor-out/topics*.txt file, convert it to CSV.

Script
------
You can run the generate_everything.sh script to automatically harvest and process all possible data based on the raw-texts directory.
