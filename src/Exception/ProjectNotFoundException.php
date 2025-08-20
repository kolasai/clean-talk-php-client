<?php

namespace CleanTalk\Exception;

use Throwable;

class ProjectNotFoundException extends CleanTalkException
{
    public const CODE = 'PROJECT_NOT_FOUND';

    public function __construct($message, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
