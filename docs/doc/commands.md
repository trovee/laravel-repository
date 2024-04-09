# Commands

## List of Commands

- [make:repository](#makerepository)
- [make:criteria](#makecriteria)
- [make:hook](#makehook)

## make:repository

This command creates a repository for the given model. If the model doesn't exist, it will create the model as well.

```bash
php artisan make:repository
# or
php artisan make:repository --model User
```


## make:criteria

This command creates a criteria for you to use in your repository.

```bash
php artisan make:criteria ActiveUsers
```

## make:hook

This command creates a hook for you to use in your repository.

```bash
php artisan make:hook UserCreatedHook
```
