<?php

namespace Tests\Request;

use CleanTalk\Request\Message;
use CleanTalk\Exception\CleanTalkException;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testCanCreateMessageWithValidData(): void
    {
        $msg = new Message('id123', 'Hello world!');
        $actual = $msg->jsonSerialize();
        $this->assertEquals(['messageId' => 'id123', 'message' => 'Hello world!'], $actual);
    }

    public function testThrowsExceptionIfMessageIdIsEmpty(): void
    {
        $this->expectException(CleanTalkException::class);
        new Message('', 'Some text');
    }

    public function testThrowsExceptionIfTextIsEmpty(): void
    {
        $this->expectException(CleanTalkException::class);
        new Message('id123', '');
    }
}

