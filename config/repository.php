<?php

return [

    'default_repository' => Trovee\Repository\Eloquent\DefaultRepository::class,

    'should_be_strict' => true,

    'history' => [
        'enabled' => true,
        'max_event_to_remember' => 10,
    ],

    'bindings' => [
        //  App\Repositories\UserRepository::class => App\Repositories\Eloquent\UserRepository::class // or 'default',
    ],

];
