<?php

namespace CleanTalk;

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
        $obj = new self();
        $obj->messageId = $data['messageId'];
        $obj->message = $data['message'];
        $obj->categories = (array)$data['categories'];
        $obj->prediction = $data['prediction'];
        $obj->probability = (float)$data['probability'];
        return $obj;
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
