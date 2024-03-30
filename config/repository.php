<?php

return [

    'default_repository' => Trovee\Repository\Eloquent\DefaultRepository::class,

    'should_be_strict' => true,

    'bindings' => [
        //  App\Repositories\UserRepository::class => App\Repositories\Eloquent\UserRepository::class // or 'default',
    ],

];
