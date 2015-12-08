#!/bin/bash

for file in raw-texts/*; do
    echo Processing $file...
    echo Harvesting AlchemyAPI...
    php HarvestAlchemyApi.php $file
    echo Harvesting Spotlight...
    php HarvestSpotlight.php $file
    echo Harvesting TextRazor...
    php HarvestTextRazor.php $file
done

echo Creating CSV summaries of AlchemyAPI entities...
for file in alchemyapi-out/entities*.txt; do
    php AlchemyApiEntitiesToCsv.php $file
done

echo Creating CSV summaries of Spotlight entities...
for file in raw-texts/*; do
    base=`basename $file`
    php SpotlightEntitiesToCsv.php spotlight-out/${base/.txt/*.json}
    php SpotlightEntitySummarizer.php spotlight-out/${base/.txt/.csv}
done

echo Creating CSV summaries of TextRazor entities...
for file in raw-texts/*; do
    base=`basename $file`
    php TextRazorEntitiesToCsv.php textrazor-out/entities*${base}
done
for file in textrazor-out/entities*.csv; do
    php TextRazorEntitySummarizer.php $file
done

echo Creating CSV summaries of TextRazor topics...
for file in textrazor-out/topics*.txt; do
    php TextRazorTopicsToCsv.php $file
done

echo Merging AlchemyAPI and TextRazor entity files...
if [ ! -d combined-out ]; then
    mkdir combined-out
fi
for file in raw-texts/*; do
    base=`basename $file`
    suffix=${base/.txt/.csv}
    echo "-- *$suffix --"
    php CombineEntities.php alchemyapi-out/entities-$suffix spotlight-out/summary-combined-entities-$suffix textrazor-out/summary-combined-entities-$suffix combined-out/merged-entities-$suffix
done
