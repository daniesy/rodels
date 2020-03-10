
# Rodels

Remote models for Laravel.

## Getting Started

Follow these instructions to get Rodels up and running in your Laravel project. 

### Prerequisites

You need **Composer** installed on your machine, but since you're already using Laravel, I guess you can skip this step.

### Installing

Begin by installing this package through Composer.

```bash
composer require daniesy/rodels
```

### Laravel Users

For Laravel users, there is a service provider you can make use of to automatically register the necessary bindings.

> Laravel 5.5+ users: this step may be skipped, as we can auto-register the package with the framework.

```php
// config/app.php
'providers' => [
	'...',
	Daniesy\Rodels\RodelsServiceProvider::class
],

'aliases' => [
	'...',
	'Remote' => \Daniesy\Rodels\Facade\Remote::class,
],
```
When this provider is booted, you'll gain access to a helpful  `Remote`  facade, which you may use in your controllers.

```php
public function index()
{
	$users = Remote::users()->list();
	return response()->json($users); 
}
```
> In Laravel 5, of course add  `use Remote;`  to the top of your controller.

### Defaults

If using Laravel, there are only two configuration options that you'll need to worry about. First, publish the default configuration

```bash
php artisan vendor:publish

// Or...

php artisan vendor:publish --provider="Daniesy\Rodels\RodelsServiceProvider"
```
This will add a new configuration file to:  `config/rodels.php`.

```php
<?php
return [  
/*  
 |-------------------------------------------------------------------------- 
 | The remote API host 
 |-------------------------------------------------------------------------- 
 | 
 | Set this value to the url of the remote API you want to connect to | 
*/  

  'host' => env('RODELS_HOST'),  
  
/*  
 |-------------------------------------------------------------------------- 
 | The HTTP client used to send HTTP requests 
 |-------------------------------------------------------------------------- 
 | 
 | This option defines the http client that rodels will be using to connect 
 | to the remote API. 
 | 
 | Supported: "curl" 
*/  

  'client' => 'curl',  
  
/*  
 |-------------------------------------------------------------------------- 
 | The authentication method 
 |-------------------------------------------------------------------------- 
 | 
 | You can set the authentication method that will be used when connecting 
 | to the API. 
 | 
 | Supported: "key" 
*/  

  'auth' => 'key',  
  
/*  
 |-------------------------------------------------------------------------- 
 | Authentication configuration 
 |-------------------------------------------------------------------------- 
 | 
 | Configure the key authentication method. 
 | 
*/  

  'key' => [  
  'name' => 'api-key',  
  'value' => env('RODELS_KEY')  
 ],
];
```

## Using the library.

We add two artisan commands, one will create endpoints and the other one rodels.

### Endpoints

The first step when using Rodels is to create an endpoint.
```bash
php artisan make:endpoint Users -r
```

This will create a new class in `app\Endpoints`- in this case `Users.php`.
In this class you should define all methods related to your endpoint.

> All endpoints names should be nouns in plural form

Using the `-r` or `--rodel` option, automatically creates a rodel for this endpoint.

```php
<?php  
  
namespace App\Endpoints;  
  
  
use App\Rodels\User;  
use Daniesy\Rodels\Api\Components\Endpoint;  
use Daniesy\Rodels\Api\Components\RodelCollection;  
use Daniesy\Rodels\Api\Exceptions\InvalidModelException;  
  
class Users extends Endpoint  
{  
	/**  
	* @param array $params  
 	* @return RodelCollection  
	* @throws InvalidModelException  
	*/  
	public function list(array $params = []) : RodelCollection  
	{  
		$response = $this->authRequest()->get("users", $params);  
		return new RodelCollection($response, User::class);  
	}  
	
	/**  
	* @param string $name  
	* @return User  
	*/
	public function find(string $name) : User  
	{  
		$response = $this->authRequest()->get("users", compact('name'));  
		return new User($response);  
	}  
}

``` 

### Rodels

For every endpoint, there should be a rodel created as well. Rodels are like Models, but instead of using them for databases, they're used for remote APIs.

Create a new rodel with this command:

```
php artisan make:rodel User
```

This will create a new rodel class in `app\Rodels`- in this case `User.php`.

> All rodels names should be nouns in singular form.

