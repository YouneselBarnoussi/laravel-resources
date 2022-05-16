

![laravel-resources-dark-yellow](https://user-images.githubusercontent.com/45201651/168611579-ab08c395-d4fd-4e97-b9d8-3e78e6cb1beb.png#gh-dark-mode-only)
![laravel-resources-light-yellow](https://user-images.githubusercontent.com/45201651/168611772-2504e3dd-2e78-46da-adf4-fcbbbf287154.png#gh-light-mode-only)



[![Latest Version on Packagist](https://img.shields.io/packagist/v/owowagency/laravel-resources.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-permission)
![](https://github.com/owowagency/package-name/workflows/Run%20Tests/badge.svg?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/owowagency/laravel-resources.svg?style=flat-square)](https://packagist.org/packages/owowagency/laravel-resources)

Create api endpoints with ease by only defining routes.

```php
Route:apiResource(
    'posts',
    ResourceController::class,
    [
        'model' => Post::class,
        'requests' => [
            'store' => StoreRequest::class,
            'update' => UpdateRequest::class,
        ],
    ],
);
```

# Table of contents

1. [Installation](#installation)
2. [Usage](#usage)
    1. [Route registration](#route-registration)
    2. [Customizing controllers](#customizing-controllers)
    3. [Eloquent API resources](#eloquent-api-resources)
    4. [Configuration](#configuration)
3. [Contributing](#contributing)
4. [Security](#security)
5. [Credits](#credits)
6. [License](#license)

# Installation

Installing this package can be done easily via the following Composer command.

```bash
composer require owowagency/laravel-resources
```

# Usage

## Route registration

This package adds a few new features to the already existing `apiResource` method, `model` and `requests`.

`model` will be used to determine what model is being be handled. <br>
`requests` should contain the form requests that are used to validate incoming data during creation and updating.


Example:
```php
use App\Http\Requests\Posts\StoreRequest;
use App\Http\Requests\Posts\UpdateRequest;
use App\Models\Post;
use OwowAgency\LaravelResources\Controllers\ResourceController;

Route:apiResource(
    'posts',
    ResourceController::class,
    [
        'model' => Post::class,
        'requests' => [
            'store' => StoreRequest::class,
            'update' => UpdateRequest::class,
        ],
    ],
);
```

## Customizing controllers

All methods in the [`ResourceController`](https://github.com/owowagency/laravel-resources/blob/master/src/Controllers/ResourceController.php) can be overwritten. We made it a little easier by adding methods like `indexModel`, and `updateModel`. You will not have to worry about validating, authorization, or returning the models as response.

```php
use OwowAgency\LaravelResources\Controllers\ResourceController;

class PostController extends ResourceController
{
    /**
     * Returns models instances used for the index action.
     * 
     * @return mixed
     */
    public function indexModel()
    {
        return Post::where('title', 'LIKE', request('search'))->paginate();
    }

    /**
     * Updates and returns the model instance for the update action.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function updateModel(Request $request, Model $model)
    {
        $model->update(['user_id' => \Auth::user()->id]);
    }
}
```

## Eloquent API resources

This package will always try to return the API resource representation of the specified model. It applies auto discovery to determine what resource to use.

By default it will use the following pattern to discover the resource class:
```
App\Http\Resources\{class_baseName($modelClass)}Resource
```
In case of a `Post` model that will become:
```
App\Http\Resources\PostResource
```

## Configuration

Configuration can be published with:
```
php artisan vendor:publish --tag=laravelresources
```

```php
return [

    /**
     * Is used for auto discovery of http resources. Allows for placing
     * resources under a different namespace.
     */
    'resource_namespace' => 'App\\Http\\Resources',

    /**
     * Configure resources that do not follow the default auto discovery rules.
     * 
     * Eg:
     * [Post::class => SpecialPostResource::class]
     */
    'resource_factory' => [],

];
```

# Contributing

Please see [CONTRIBUTING](https://github.com/owowagency/package-name/blob/main/CONTRIBUTING.md) for details.

# Security

If you discover any security-related issues, please email dees@owow.io instead of using the issue tracker.

# Credits

- [Dees Oomens](https://github.com/dees040)

Something about OWOW and that we're looking for talent?

# License

The MIT License (MIT). Please see [License File](https://github.com/owowagency/package-name/blob/main/LICENSE.md) for more information.
