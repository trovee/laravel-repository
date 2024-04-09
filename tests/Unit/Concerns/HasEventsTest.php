<?php

use Mockery as m;
use Trovee\Repository\Concerns\HasEvents;
use Trovee\Repository\Contracts\HookInterface;
use Trovee\Repository\Eloquent\AbstractRepository;

class HasEventsTestClass extends AbstractRepository
{
    use HasEvents;

    public function onCustomEvent($repository, $arg = null)
    {
    }
}

beforeEach(function () {
    $this->testClass = new HasEventsTestClass();

    $this->hook = m::mock(HookInterface::class);
    $this->classMock = m::mock(HasEventsTestClass::class);
});

it('can trigger method hooks', function () {
    $this->classMock
        ->shouldReceive('onCustomEvent')->once()->with($this->testClass, 'arg1');

    $this->classMock->onCustomEvent($this->testClass, 'arg1');
});
