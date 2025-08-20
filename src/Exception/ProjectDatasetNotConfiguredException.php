<?php

namespace CleanTalk\Exception;

use Throwable;

class ProjectDatasetNotConfiguredException extends CleanTalkException
{
    public const CODE = 'PROJECT_DATASET_NOT_CONFIGURED';

    public function __construct($message, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
