DimeExtraction
==============

Introduction
------------
These tools were created to experiment with entity extraction from dime novels.

Sample Files
------------
The raw-texts directory contains some dime novel texts extracted from Project Gutenberg.

Setting Up
----------
Paste your AlchemyApi API key into api_key.txt and your TextRazor API key into textrazor.key.

Tools
-----
These tools allow retrieval of data from various APIs:
  - HarvestAlchemyApi.php [filename] - Dump information about [filename] into the alchemyapi-out directory.
  - HarvestTextRazor.php [filename] - Dump information about [filename] into the textrazor-out directory.

These tools allow processing of the retrieved data:
  - AlchemyApiEntitiesToCsv.php [filename] - Where [filename] is an alchemyapi-out/entities*.txt file, convert it to CSV.
  - DumpSerializedFile.php [filename] - Display the raw contents of [filename] (all data is stored as serialized PHP).
  - TextRazorEntitiesToCsv.php [filename] - Where [filename] is a textrazor-out/entities*.txt file, convert it to CSV.
  - TextRazorEntitySummarizer.php [filename] - Where [filename] is an output file from TextRazorEntitiesToCsv.php (or several such output files concatenated together), create a summary-*.csv file summarizing its contents.
  - TextRazorTopicsToCsv.php [filename] - Where [filename] is a textrazor-out/topics*.txt file, convert it to CSV.

Script
------
You can run the generate_everything.sh script to automatically harvest and process all possible data based on the raw-texts directory.
