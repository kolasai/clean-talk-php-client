<?php

namespace CleanTalk;

use CleanTalk\Exception\CleanTalkException;

class Message
{
    /**
     * @var string
     */
    private $messageId;
    /**
     * @var string
     */
    private $text;

    /**
     * @param string $messageId Unique identifier for the message.
     * @param string $text The content of the message.
     * @throws CleanTalkException If the message ID or text is empty.
     */
    public function __construct(string $messageId, string $text)
    {
        if (empty($messageId)) {
            throw new CleanTalkException('Message ID cannot be empty.');
        }

        if (empty($text)) {
            throw new CleanTalkException('Message text cannot be empty.');
        }

        $this->messageId = $messageId;
        $this->text = $text;
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
