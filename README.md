# Simple repositories for Laravel

#### Package is currently under development !!

### Info

Simple package which allows to implement repository pattern in Laravel applications.
Inspired by [Bosnadev](https://github.com/bosnadev/repository) package.

### Installation

```sh
composer require deseco/repository
```

Add service provider into config/app.php
```php
$providers = [
    ....
   
    Deseco\Repositories\ServiceProvider::class,
]; 
```

Add facade into config/app.php
```php
$aliases = [
    ....
    
    'Repo' => Deseco\Repositories\Facade\RepositoryFacade::class, 
]; 
```

Publish configuration
```sh
php artisan vendor:publish  --provider="Deseco\Repositories\ServiceProvider"
```

Setup configuration for repositories like:
* namespace - where do you store your repositories classes
* suffix - for repositories classes e.g.: `Repository`

### Usage

Create repository e.g: ClientsRepository

```php
use App\Models\Client
use Deseco\Repositories\Eloquent\Repository

class ClientsRepository extends Repository
{
    public function model()
    {
        // specify model
        return Client::class;
    }
}
```

Then you can use name without suffix to get repository instance.

```php
Repo::clients()->all();
```

or inject repository into class/method and use standalone:

```php
public function index(ClientsRepository $clientsRepository)
{
    $clients = $clientsRepository->all();
}
```





