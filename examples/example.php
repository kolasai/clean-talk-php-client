<?php

use CleanTalk\CleanTalkPredictionClient;
use CleanTalk\KolasAiOAuthClient;
use CleanTalk\Request\Message;
use CleanTalk\Request\PredictRequest;

require 'vendor/autoload.php';

const YOUR_PROJECT_ID = '11';
const YOUR_CLIENT_ID = '4';
const YOUR_CLIENT_SECRET = 'zYFtjzSv2JKvAE6cjojxuGyjcYsAi6Fxmiv4rrjF';

$oauthClient = new KolasAiOAuthClient();
$authResult = $oauthClient->auth(YOUR_CLIENT_ID, YOUR_CLIENT_SECRET);

$client = new CleanTalkPredictionClient($authResult->getAccessToken());
$response = $client->predict(
    new PredictRequest(
        YOUR_PROJECT_ID,
        [
            new Message('11177c92-1266-4817-ace5-cda430481111', 'Hello world!'),
            new Message('22277c92-1266-4817-ace5-cda430482222', 'Good buy world!'),
        ]
    )
);

foreach ($response->getPredictions() as $prediction) {
    echo "MessageId: {$prediction->getMessageId()}\n";
    echo "Message: {$prediction->getMessage()}\n";
    echo "Prediction: {$prediction->getPrediction()}\n";
    echo "Probability: {$prediction->getProbability()}\n";
    echo "Categories: " . implode(', ', $prediction->getCategories()) . "\n";
}

$client->asyncPredict(new PredictRequest(
    YOUR_PROJECT_ID,
    [
        new Message('11177c92-1266-4817-ace5-cda430483333', 'Hello world!'),
        new Message('22277c92-1266-4817-ace5-cda430484444', 'Good buy world!'),
    ]
));
