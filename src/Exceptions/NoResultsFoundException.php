<?php

namespace Trovee\Repository\Exceptions;

use Exception;

class NoResultsFoundException extends Exception
{
    public function __construct(protected string $model)
    {
        parent::__construct("No results found for model: {$this->model}", 404);
    }
}
