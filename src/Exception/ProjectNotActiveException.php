<?php

namespace CleanTalk\Exception;

use Throwable;

class ProjectNotActiveException extends CleanTalkException
{
    public const CODE = 'PROJECT_NOT_ACTIVE';

    public function __construct($message, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
