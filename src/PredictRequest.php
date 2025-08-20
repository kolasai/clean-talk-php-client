<?php

namespace CleanTalk;

class PredictRequest
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
        $this->messages = $messages;
        $this->projectId = $projectId;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }
}
