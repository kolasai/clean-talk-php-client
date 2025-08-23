<?php

namespace Tests\Request;

use CleanTalk\Request\PredictRequest;
use CleanTalk\Request\Message;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PredictRequestTest extends TestCase
{
    public function testCanCreateWithValidData(): void
    {
        $messages = [new Message('id1', 'text1'), new Message('id2', 'text2')];
        $request = new PredictRequest('project123', $messages);

        $actual = json_decode(json_encode($request->jsonSerialize()), true);

        $this->assertEquals(
            [
                'projectId' => 'project123',
                'messages' => array_map(static function (Message $message) {
                    return $message->jsonSerialize();
                }, $messages),
            ],
            $actual
        );
    }

    public function testThrowsExceptionIfMessagesArrayIsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PredictRequest('project123', []);
    }

    public function testThrowsExceptionIfProjectIdIsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PredictRequest('', [new Message('id1', 'text1')]);
    }

    public function testJsonSerialize(): void
    {
        $messages = [new Message('id1', 'text1')];
        $request = new PredictRequest('project123', $messages);
        $expected = [
            'projectId' => 'project123',
            'messages' => $messages,
        ];
        $this->assertEquals($expected, $request->jsonSerialize());
    }
}

