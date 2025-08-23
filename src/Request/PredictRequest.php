<?php

namespace CleanTalk\Request;

use InvalidArgumentException;
use JsonSerializable;

class PredictRequest implements JsonSerializable
{
    /**
     * @var Message[]
     */
    private $messages;
    /**
     * @var string
     */
    private $projectId;

    /**
     * @param string $projectId
     * @param Message[] $messages
     */
    public function __construct(string $projectId, array $messages)
    {
        if (empty($messages)) {
            throw new InvalidArgumentException('Messages array cannot be empty.');
        }

        if (empty($projectId)) {
            throw new InvalidArgumentException('Project ID cannot be empty.');
        }

        $this->messages = $messages;
        $this->projectId = $projectId;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'projectId' => $this->projectId,
            'messages' => $this->messages,
        ];
    }
}
