<?php

namespace NationalFloodExperts\NCAT\Exceptions;

use Exception;

class RequiredParameterMissingException extends Exception
{
    public function __construct(string $parameterName)
    {
        parent::__construct("Missing Parameter '$parameterName'");
    }
}
