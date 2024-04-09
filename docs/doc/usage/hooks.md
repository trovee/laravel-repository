# Using Hooks

Hooks are similar to events. Compare to events hooks are lightweight and synchronous. They are used to modify the
behavior of the system, or to add new functionality.

## Creating Hooks

Hooks are simple PHP classes that implement the `HookInterface`. The interface has only one method, `onTrigger`.
You can also create hooks using the `make:hook` artisan command.

```bash
php artisan make:hook SomeEventHook
```

```php
// app/Hooks/SomeEventHook.php
namespace App\Hooks;

use Trovee\Repository\Contracts\HookInterface;
use App\Repositories\UserRepository;

class SomeEventHook implements HookInterface
{
    public function onTrigger(UserRepository $repository, $data)
    {
        // Do something when a hooked event is triggered
    }
}
```

## Registering Hooks

You can add hooks to the repository by calling the `addHook` method. You can add multiple hooks to the repository.

```php
// anywhere in your code
use Trovee\Repository\Facades\Repository;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Hooks\SomeEventHook;

Repository::get(User::class)
    ->addHook('someEvent', function (UserRepository $repository, $data) {
        // Do something when a user is created
    })
    ->addHook('someEvent', SomeEventHook::class)
```

When you trigger the `someEvent` event, the hook will be executed. This is how you can trigger the event:

```php
// UserRepository.php
class UserRepository extends AbstractRepository implements UserRepositoryContract
{
    public function someOperation(array $data)
    {
        // do something
        $this->trigger('someEvent', $data);
        // return something
    }
}
```

Let's say you don't want to create Hook class or to use a closure. Trovee Repository assumes that you have a method in
your repository that starts with `on` and followed by the event name. If you have a method like `onSomeEvent` in your
repository, Trovee Repository will call this method when the event is triggered.

```php
// UserRepository.php
class UserRepository extends AbstractRepository implements UserRepositoryContract
{
   public function someOperation(array $data)
   {
         // do something
         $this->trigger('someEvent', $data);
         // return something
   }
    
    public function onSomeEvent(array $data)
    {
        // do something
    }
}
``` 

> [!NOTE]
> Please note that all of the hooking methods will be executed if they are coexist.
