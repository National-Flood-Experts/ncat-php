<?php

namespace NationalFloodExperts\NCAT\Exceptions;

use Exception;

class CannotConnectToNCATException extends Exception
{
    public function __construct(string $reason)
    {
        parent::__construct("Cannot connnect to NCAT: $reason");
    }
}
