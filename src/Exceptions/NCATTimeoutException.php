<?php

namespace NationalFloodExperts\NCAT\Exceptions;

use Exception;

class NCATTimeoutException extends Exception
{
    public function __construct()
    {
        parent::__construct("The connection to the NCAT server has timed out");
    }
}
