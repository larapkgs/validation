<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Package Generator Templates
    |--------------------------------------------------------------------------
    |
    | This array controls the defaults for the different architectural blueprints
    | your package can generate. Each blueprint can customize its own root path,
    | namespace prefix, and target folder directory.
    |
    */
    'generators' => [
        'validation' => [
            'base_path' => app_path(),
            'base_namespace' => 'App\\',
            'directory' => 'Validation',
        ]
    ]
];