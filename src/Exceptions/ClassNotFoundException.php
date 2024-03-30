<?php

namespace Trovee\Repository\Exceptions;

use Throwable;

class ClassNotFoundException extends ClassException
{

    public function __construct(string $fqcn, ?Throwable $previous = null)
    {
        $message = "Failed when trying to access $fqcn. Class does not exists.";

        parent::__construct($message, 0, $previous);
    }

}
