#!/bin/bash

ALCHEMY_CHUNK_SIZE=25000

for file in raw-texts/*; do
    echo Processing $file...
    echo Harvesting AlchemyAPI...
    php HarvestAlchemyApi.php $file $ALCHEMY_CHUNK_SIZE
    echo Harvesting Spotlight...
    php HarvestSpotlight.php $file
    echo Harvesting TextRazor...
    php HarvestTextRazor.php $file
done

echo Creating CSV summaries of AlchemyAPI entities...
for file in raw-texts/*; do
    base=`basename $file`
    files=""
    for name in alchemyapi-out/entities*${base}; do
        files="$files $name"
    done
    php AlchemyApiEntitiesToCsv.php $files
done
for file in alchemyapi-out/entities*.csv; do
    php AlchemyApiEntitySummarizer.php $file
done

echo Creating CSV summaries of Spotlight entities...
for file in raw-texts/*; do
    base=`basename $file`
    files=""
    for name in spotlight-out/${base/.txt/*.json}; do
        files="$files $name"
    done
    php SpotlightEntitiesToCsv.php $files
    php SpotlightEntitySummarizer.php spotlight-out/${base/.txt/.csv}
done

echo Creating CSV summaries of TextRazor entities and topics...
for file in raw-texts/*; do
    base=`basename $file`
    files=""
    for name in textrazor-out/entities*${base}; do
        files="$files $name"
    done
    php TextRazorEntitiesToCsv.php $files
    files=""
    for name in textrazor-out/topics*${base}; do
        files="$files $name"
    done
    php TextRazorTopicsToCsv.php $files
done
for file in textrazor-out/entities*.csv; do
    php TextRazorEntitySummarizer.php $file
done

echo Merging AlchemyAPI and TextRazor entity files...
if [ ! -d combined-out ]; then
    mkdir combined-out
fi
for file in raw-texts/*; do
    base=`basename $file`
    suffix=${base/.txt/.csv}
    echo "-- *$suffix --"
    php CombineEntities.php alchemyapi-out/entities-0-$suffix spotlight-out/summary-$suffix textrazor-out/summary-entities-$suffix combined-out/merged-entities-$suffix
done
