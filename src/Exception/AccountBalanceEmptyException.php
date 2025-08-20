<?php

namespace CleanTalk\Exception;

use Throwable;

class AccountBalanceEmptyException extends CleanTalkException
{
    public const CODE = 'ACCOUNT_BALANCE_EMPTY';

    public function __construct($message, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
