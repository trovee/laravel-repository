# Getting Started

## Requirements

`trovee/laravel-repository` requires following dependencies to oparate properly:

| Dependency | Version |
|------------|---------|
| PHP        | ^8.1    |
| Laravel    | ^10.48  |

## Installation

You can install the package via composer.

```bash
composer require trovee/laravel-repository
```

After installing the package, you need to trigger the installation command.

```bash
php artisan repository:install
```

Install command publishes a configuration file for you to configure the behavior of the package. This configuration
lives in `config/repository.php` and its content as follows:

```php
// config/repository.php
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

```

Check out the [Configuration](./doc/configuration?version=1.x) section if you need more detailed information about.

