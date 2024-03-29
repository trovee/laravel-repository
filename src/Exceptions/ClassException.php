<?php

namespace Trovee\Repository\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Trovee\Repository\Contracts\RepositoryInterface;

class ClassException extends Exception
{

    public static function doesNotExists(string $fqcn, $previous = null)
    {
        return new static("Failed when trying to access $fqcn. Class does not exists.", 0, $previous);
    }

    public static function mustImplement(string $fqcn, string $interface, $previous = null)
    {
        return new static("Failed when trying to access $fqcn. Class must implement $interface.", 0, $previous);
    }

    public static function mustBeAnInstanceOf(string $fqcn, string $instance, $previous = null)
    {
        return new static("Failed when trying to access $fqcn. Class must be an instance of $instance.", 0, $previous);
    }

    public static function isNotModel(string $model)
    {
        return self::mustBeAnInstanceOf($model, Model::class);
    }

    public static function isNotRepository(string $repository)
    {
        return self::mustImplement($repository, RepositoryInterface::class);
    }

}
