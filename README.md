# Laravel Repository

An up to date repository pattern implementation for Laravel 10+.

## Installation

```bash
composer require trovee/laravel-repository
```

### Publish the configuration file

```bash
php artisan repository:install
```

## Usage

### Create a new repository

```bash
php artisan make:repository UserRepository -m
```

After running the command above, you have an interface like `App\Repositories\UserRepository` and an implementation like `App\Repositories\Eloquent\UserRepository`.
also, you can create custom repositories by implementing the `App\Repositories\UserRepository` interface. If you need to bind your custom repository to the interface, you need to update the configuration file.


### Binding the repository to your model

```php
// src/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Trovee\Repository\Attributes\Repository;
use App\Repositories\UserRepository;

#[Repository(UserRepository::class)]
class User extends Model
{
    //...
}
```

### Using the repository
```php
use Trovee\Repository\Facades\Repository;
use App\Repositories\UserRepository;
use App\Models\User;

$userRepository = Repository::get(User::class); // returns an instance of App\Repositories\Eloquent\UserRepository

// or

$userRepository = app(UserRepository::class); // returns an instance of App\Repositories\Eloquent\UserRepository

#######

$userRepository->all(); // returns all users 
```
