<?php
namespace Tests\Response;

use CleanTalk\Exception\CleanTalkException;
use CleanTalk\Response\PredictionResult;
use PHPUnit\Framework\TestCase;

class PredictionResultTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     */
    public function testFromArrayWithValidData($data, $expected): void
    {
        $result = PredictionResult::fromArray($data);
        $this->assertInstanceOf(PredictionResult::class, $result);
        $this->assertEquals($expected['messageId'], $result->getMessageId());
        $this->assertEquals($expected['message'], $result->getMessage());
        $this->assertEquals($expected['prediction'], $result->getPrediction());
        $this->assertEquals($expected['probability'], $result->getProbability());
        $this->assertEquals($expected['categories'], $result->getCategories());
    }

    public static function validDataProvider(): array
    {
        return [
            'basic valid' => [
                [
                    'messageId' => 'msg1',
                    'message' => 'Test message',
                    'prediction' => 'spam',
                    'probability' => 0.99,
                    'categories' => ['spam', 'test']
                ],
                [
                    'messageId' => 'msg1',
                    'message' => 'Test message',
                    'prediction' => 'spam',
                    'probability' => 0.99,
                    'categories' => ['spam', 'test']
                ]
            ],
            'extra fields' => [
                [
                    'messageId' => 'msg1',
                    'message' => 'Test message',
                    'prediction' => 'ham',
                    'probability' => 0.01,
                    'categories' => ['ham'],
                    'extra' => 'should be ignored'
                ],
                [
                    'messageId' => 'msg1',
                    'message' => 'Test message',
                    'prediction' => 'ham',
                    'probability' => 0.01,
                    'categories' => ['ham']
                ]
            ]
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testFromArrayWithInvalidData($data): void
    {
        $this->expectException(CleanTalkException::class);
        PredictionResult::fromArray($data);
    }

    public static function invalidDataProvider(): array
    {
        return [
            'missing message' => [
                [
                    'messageId' => 'msg1',
                    // 'message' is missing
                    'prediction' => 'spam',
                    'probability' => 0.99,
                    'categories' => ['spam', 'test']
                ]
            ],
            'empty categories' => [
                [
                    'messageId' => 'msg1',
                    'message' => 'Test message',
                    'prediction' => 'spam',
                    'probability' => 0.99,
                    'categories' => []
                ]
            ],
            'invalid probability type' => [
                [
                    'messageId' => 'msg1',
                    'message' => 'Test message',
                    'prediction' => 'spam',
                    'probability' => 'not-a-float',
                    'categories' => ['spam']
                ]
            ]
        ];
    }
}
