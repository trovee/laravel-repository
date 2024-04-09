# Quick Start

## Calling the repository

Great news! You don't have to configure anything if you just want to use the basic repository operations. You can simply
call:

```php
use Trovee\Repository\Facades\Repository;
use App\Models\User;

Repository::get(User::class)->getById($user_id);
```

#### "Well, how does it work though" you may ask...

Trovee Repository tries to detect any repository could be related to `App\Models\User` model and initiates the
repository. But if there's no repository that relatable to the model, Trovee Repository
relates `Trovee\Repository\Eloquent\DefaultRepository` to the model. **And voila!** You can use the basic repository methods.

## Creating a repository

The package comes with a set of well designed [Commands](./doc/commands?version=latest). But in this section we will
just need the `make:repository` command. You can simply create and configure everything you need, by just calling the
command.

```bash
php artisan make:repository
# or
php artisan make:repository --model User
```

Let's break those commands up. If you pass `--model [ModelName]` option to the command, it will automatically create and
update necessary files. If you run the command without any option. The command will ask you to provide a model name. If
given model doesn't exists, command will create the model. Here is a file tree for you to see the created files:

```
app/
  ├── Models/
      ├── User.php
  ├── Repositories/
      ├── Contracts/
          ├── UserRepositoryContract.php
      ├── Eloquent/
          ├── UserRepository.php

```
