<?php
namespace Tests\Response;

use CleanTalk\Exception\CleanTalkException;
use CleanTalk\Response\PredictionResult;
use CleanTalk\Response\PredictResponse;
use PHPUnit\Framework\TestCase;

class PredictResponseTest extends TestCase
{
    public function testFromArrayWithValidPredictions(): void
    {
        $data = [
            'predictions' => [
                [
                    'messageId' => 'msg1',
                    'message' => 'Test message 1',
                    'prediction' => 'spam',
                    'probability' => 0.95,
                    'categories' => ['spam', 'test']
                ],
                [
                    'messageId' => 'msg2',
                    'message' => 'Test message 2',
                    'prediction' => 'ham',
                    'probability' => 0.05,
                    'categories' => ['ham', 'test']
                ]
            ]
        ];
        $response = PredictResponse::fromArray($data);
        $predictions = $response->getPredictions();

        $this->assertCount(2, $predictions);
        $this->assertInstanceOf(PredictionResult::class, $predictions[0]);
        $this->assertEquals('spam', $predictions[0]->getPrediction());
        $this->assertEquals(0.95, $predictions[0]->getProbability());
        $this->assertEquals('ham', $predictions[1]->getPrediction());
        $this->assertEquals(0.05, $predictions[1]->getProbability());
    }

    public function testFromArrayWithEmptyPredictions(): void
    {
        $data = ['predictions' => []];
        $response = PredictResponse::fromArray($data);

        $this->assertIsArray($response->getPredictions());
        $this->assertCount(0, $response->getPredictions());
    }

    public function testFromArrayWithEmptyPredictionsKey(): void
    {
        $data = [];
        $response = PredictResponse::fromArray($data);

        $this->assertIsArray($response->getPredictions());
        $this->assertCount(0, $response->getPredictions());
    }

    public function testFromArrayWithInvalidPredictionsType(): void
    {
        $this->expectException(CleanTalkException::class);

        PredictResponse::fromArray(['predictions' => 'not-an-array']);
    }
}
