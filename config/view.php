<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    'prefixes' => [
        'layouts' => 'layouts',
        'blocks' => 'templates.blocks',
        'macros' => 'templates.macros',
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

    /*
    |--------------------------------------------------------------------------
    | Autogeneration
    |--------------------------------------------------------------------------
    |
    | This option determines which Twig views will be created for a resource
    | when calling artisan make:view {name} --resource
    |
    */

    'generate' => [
        'index', 'show', 'create', 'edit'
    ],
];
