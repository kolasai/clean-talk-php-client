<?php

namespace CleanTalk\Response;

use CleanTalk\Exception\CleanTalkException;

class PredictResponse
{
    /** @var PredictionResult[] */
    private $predictions = [];

    /**
     * @param string[][]|float[][] $data
     * @return self
     * @throws CleanTalkException
     */
    public static function fromArray(array $data): self
    {
        $self = new self();
        if (isset($data['predictions'])) {
            if (!is_array($data['predictions'])) {
                throw new CleanTalkException('Predictions must be an array');
            }
            foreach ($data['predictions'] as $item) {
                $self->predictions[] = PredictionResult::fromArray($item);
            }
        }
        return $self;
    }

    /**
     * @return PredictionResult[]
     */
    public function getPredictions(): array
    {
        return $this->predictions;
    }
}
