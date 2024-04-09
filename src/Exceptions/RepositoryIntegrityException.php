<?php

namespace Trovee\Repository\Exceptions;

use Throwable;

class RepositoryIntegrityException extends ClassException
{
    public function __construct(
        string $action = 'create',
        string $fqcn = '',
        string $verb = 'implement',
        string $inheritance = '',
        ?Throwable $previous = null
    ) {
        $is_class = interface_exists($inheritance) ? 'Interface' : 'Class';
        $message = "Failed when trying to {$action} [{$fqcn}]. {$is_class} must {$verb}".
            ($inheritance ? " [{$inheritance}]." : '.');
        parent::__construct($message, 0, $previous);
    }
}
