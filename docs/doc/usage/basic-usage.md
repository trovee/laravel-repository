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
relates `Trovee\Repository\Eloquent\DefaultRepository` to the model. **And voila!** You can use the basic repository
methods.

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

After running the command, you will have an interface like `App\Repositories\Contracts\UserRepositoryContract` and an
implementation like `App\Repositories\Eloquent\UserRepository`. Also, you can create custom repositories by implementing
the `App\Repositories\Contracts\UserRepositoryContract` interface. If you need to bind your custom repository to the
interface, you need to update the configuration file.

## Creating records

You can create records by using the `create`, `createFromRequest`, `createThenReturn`, `createMany`, `firstOrCreate`
or `updateOrCreate` methods. Here is an example:

```php
// BlogPostController.php
use App\Http\Requests\CreateBlogPostRequest;
use App\Repositories\Contracts\BlogPostRepositoryContract;

public function store(CreateBlogPostRequest $request, BlogPostRepositoryContract $repository): Response 
{
    $created = $repository->createFromRequest($request); // this will return the created model
    
    return response()->json($created);
}
```

## Updating records

You can update records by using the `update`, `updateFromRequest`, `updateThenReturn`, `findAndUpdate` methods. 

```php
// BlogPostController.php

use App\Http\Requests\UpdateBlogPostRequest;
use App\Repositories\Contracts\BlogPostRepositoryContract;

public function update(UpdateBlogPostRequest $request, BlogPostRepositoryContract $repository, BlogPost $blogPost): Response 
{
    $updated = $repository->setRecord($blogPost)->updateFromRequest($request); // this will return the updated model
    
    return response()->json($updated);
}
```

## Deleting records

You can delete records by using the `delete`, `forceDelete`, `deleteThenReturn`, `deleteAllDuplicates` or  `deleteDuplicatesAndKeepOne` methods. 

```php
// BlogPostController.php

use App\Repositories\Contracts\BlogPostRepositoryContract;

public function destroy(BlogPostRepositoryContract $repository, BlogPost $blogPost): Response 
{
    $affectedRows = $repository->setRecord($blogPost)->delete(); 
    
    return response()->json($deleted);
}
```

You can also delete duplicate records by using the `deleteAllDuplicates` or `deleteDuplicatesAndKeepOne` method.

```php
// BlogPostController.php

use App\Repositories\Contracts\BlogPostRepositoryContract;

public function clear(BlogPostRepositoryContract $repository): Response 
{
    $affectedRows = $repository->deleteDuplicatesAndKeepOne(['title' => 'Lorem ipsum dolor sit amet.']);
    
    return response()->json($deleted);
}
```
