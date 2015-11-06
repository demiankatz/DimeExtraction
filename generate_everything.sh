#!/bin/bash

#for file in raw-texts/*; do
#    echo Processing $file...
#    echo Harvesting AlchemyApi...
#    php HarvestAlchemyApi.php $file
#    echo Harvesting TextRazor...
#    php HarvestTextRazor.php $file
#done

echo Creating CSV summaries of AlchemyApi entities...
for file in alchemyapi-out/entities*.txt; do
    php AlchemyApiEntitiesToCsv.php $file
done

echo Creating CSV summaries of TextRazor entities...
for file in textrazor-out/entities*.txt; do
    php TextRazorEntitiesToCsv.php $file
done
for file in raw-texts/*; do
    base=`basename $file`
    cat textrazor-out/entities*${base/.txt/.csv} > textrazor-out/combined-entities-${base/.txt/.csv}
done
for file in textrazor-out/combined-entities*; do
    php TextRazorEntitySummarizer.php $file
done

echo Creating CSV summaries of TextRazor topics...
for file in textrazor-out/topics*.txt; do
    php TextRazorTopicsToCsv.php $file
done
