<?php

namespace CleanTalk\Response;

use CleanTalk\Exception\CleanTalkException;

class PredictionResult
{
    /**
     * @var string
     */
    private $messageId;
    /**
     * @var string[]
     */
    private $categories = [];
    /**
     * @var string
     */
    private $prediction;
    /**
     * @var float
     */
    private $probability;
    /**
     * @var string
     */
    private $message;

    /**
     * @param string[]|string[][]|float[] $data
     * @return self
     * @throws CleanTalkException
     */
    public static function fromArray(array $data): self
    {
        if (
            empty($data['messageId'])
            || empty($data['message'])
            || empty($data['prediction'])
            || empty($data['probability'])
            || empty($data['categories'])
        ) {
            throw new CleanTalkException('Invalid data for PredictionResult');
        }

        if (!is_numeric($data['probability'])) {
            throw new CleanTalkException('Probability must be a numeric value');
        }

        $self = new self();
        $self->messageId = $data['messageId'];
        $self->message = $data['message'];
        $self->categories = (array)$data['categories'];
        $self->prediction = $data['prediction'];
        $self->probability = (float)$data['probability'];

        return $self;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getPrediction(): string
    {
        return $this->prediction;
    }

    public function getProbability(): float
    {
        return $this->probability;
    }
}
