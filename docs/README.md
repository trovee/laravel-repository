# Laravel Repository
## An up-to-date repository pattern implementation for Laravel

This package provides a simple and easy-to-use repository pattern implementation for Laravel. It is designed to be flexible and easy to use, allowing you to focus on your application's business logic.
Under the hood, it uses Laravel's Eloquent ORM to interact with the database. In this package we collect useful repetitive methods that we use in our projects. Feel free to contribute!


#### Here is an example
```php
// src/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Trovee\Repository\Attributes\Repository;
use App\Repositories\Contracts\UserRepositoryContract;

#[Repository(UserRepositoryContract::class)]
class User extends Model
{
    //...
}
```


-----
[![Latest Version on Packagist](https://img.shields.io/packagist/v/trovee/laravel-repository.svg?style=flat-square)](https://packagist.org/packages/trovee/laravel-repository)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/trovee/laravel-repository/tester.yaml?branch=main)](https://github.com/trovee/laravel-repository/actions/workflows/tester.yaml)
[![Total Downloads](https://img.shields.io/packagist/dt/trovee/laravel-repository.svg?style=flat-square)](https://packagist.org/packages/trovee/laravel-repository)

