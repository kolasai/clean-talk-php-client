<?php

use CleanTalk\CleanTalkPredictionClient;
use CleanTalk\KolasAiOAuthClient;

require 'vendor/autoload.php';


$oauthClient = new KolasAiOAuthClient();
$authResult = $oauthClient->auth('4', 'zYFtjzSv2JKvAE6cjojxuGyjcYsAi6Fxmiv4rrjF');

$client = new CleanTalkPredictionClient($authResult->getAccessToken());
$response = $client->predict('a2277c92-1266-4817-ace5-cda4304858a5' ,'Hello world', 11);

foreach ($response->getPredictions() as $prediction) {
    echo "MessageId: {$prediction->getMessageId()}\n";
    echo "Message: {$prediction->getMessage()}\n";
    echo "Prediction: {$prediction->getPrediction()}\n";
    echo "Probability: {$prediction->getProbability()}\n";
    echo "Categories: " . implode(', ', $prediction->getCategories()) . "\n";
}
