<?php

namespace Trovee\Repository\Exceptions;

use Throwable;

class RepositoryIntegrityException extends ClassException
{
    public function __construct(
        string $action = "create",
        string $fqcn = "",
        string $verb = "implement",
        string $inheritance = "",
        ?Throwable $previous = null
    ) {
        $message = "Failed when trying to $action [$fqcn]. Class must $verb".($inheritance ? " [$inheritance]." : ".");
        parent::__construct($message, 0, $previous);
    }

}
