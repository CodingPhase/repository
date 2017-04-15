# Simple repositories for Laravel

#### Package is currently under development !!

### Info

Simple package which allows to implement repository pattern in Laravel applications.
Inspired by [Bosnadev](https://github.com/bosnadev/repository) package. Supports criteria, query filters, and can interact with fractal.

### Installation

```sh
composer require deseco/repository
```

Add service provider into config/app.php
```php
$providers = [
    ....
   
    Deseco\Repositories\RepositoriesServiceProvider::class,
]; 
```

Publish configuration
```sh
php artisan vendor:publish  --provider="Deseco\Repositories\RepositoriesServiceProvider"
```

Setup configuration for repositories like:
* namespace - repositories namespace
* suffix - for repositories classes e.g.: `Repository`
* path - path to repositories
* class - factory repository class, when injected allows to easily create repositories (has auto-completion for Phpstorm)

### Usage

You can create repository with command:

```sh
php artisan repository:make
```

You will be asked to specify repository name 
```sh
Enter repository name::
> Clients
```
and alias (optional):
```sh
Enter alias name [clients]:
>
```
You will get two files:
```sh
Generating repository...

+-------------------+-------------------+----------+---------+
| Class             | Repository        | Property | Status  |
+-------------------+-------------------+----------+---------+
| ClientsRepository | -                 | -        | Created |
| Repositories      | ClientsRepository | clients  | Created |
+-------------------+-------------------+----------+---------+

Done!
```
Repository class (implement model method):
```php
<?php

namespace App\Repositories;

use Deseco\Repositories\Eloquent\Repository;

class ClientsRepository extends Repository
{
    /**
     * @return mixed
     */
    public function model()
    {
        // return Model::class;
    }
}
```
and RepositoriesFactory:
```php
<?php

namespace App\Repositories;

use Deseco\Repositories\Factories\RepositoryFactory;

/**
 * Class Repositories
 */
class Repositories extends RepositoryFactory
{
    /**
	 * @var ClientsRepository
	 */
	public $clients = 'clients';
}

```

Now you can use factory (you have full auto-completion for properties and methods):

```php
Route::get('/', function (\App\Repositories\Repositories $repo) {
    return $repo->clients->all();
});
```

or inject repository into class/method and use standalone:

```php
public function index(ClientsRepository $clientsRepository)
{
    $clients = $clientsRepository->all();
}
```





