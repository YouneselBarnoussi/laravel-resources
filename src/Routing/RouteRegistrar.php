<?php

namespace OwowAgency\LaravelResources\Routing;

use Illuminate\Routing\RouteRegistrar as IlluminateRouteRegistrar;

class RouteRegistrar extends IlluminateRouteRegistrar
{
    /**
     * The attributes that can be set through this class.
     *
     * @var string[]
     */
    protected $allowedAttributes = [
        'as',
        'controller',
        'domain',
        'middleware',
        'name',
        'namespace',
        'prefix',
        'scopeBindings',
        'where',
        'withoutMiddleware',
        'model',
    ];
}
