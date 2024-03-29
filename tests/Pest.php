<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Trovee\Repository\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/
expect()->extend('toHaveCountGreaterThan', function ($count) {
    if (! is_countable($this->value)) {
        throw new InvalidArgumentException('The value is not countable.');
    }

    return count($this->value) > $count;
});

expect()->extend('toHaveCountLessThan', function ($count) {
    if (! is_countable($this->value)) {
        throw new InvalidArgumentException('The value is not countable.');
    }

    return count($this->value) < $count;
});

expect()->extend('toHaveCountBetween', function ($min, $max) {
    if (! is_countable($this->value)) {
        throw new InvalidArgumentException('The value is not countable.');
    }

    $count = count($this->value);

    return $count >= $min && $count <= $max;
});

expect()->extend('toBeOneOf', function ($values) {
    return in_array($this->value, $values);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

if (! function_exists('invade')) {
    function invade(object $object, \Closure $invader, $times = 1, $args = [], $return = false): mixed
    {
        $invader = $invader->bindTo($object, $object);
        $result = null;
        for ($i = 0; $i < $times; $i++) {
            $result = $invader(...$args);
        }

        return $return ? $result : $object;
    }

}
