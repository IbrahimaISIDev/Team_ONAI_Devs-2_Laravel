<?php

return [
    'defaults' => [
        'api' => [
            'name' => 'API Documentation',
            'description' => 'API documentation pour mon projet Gestion_Boutique en Laravel',
            'version' => '1.0.0',
            'base_path' => env('SWAGGER_BASE_PATH', '/api'),
            'docs' => [
                'path' => base_path('public/docs'),
                'url' => env('SWAGGER_URL', '/swagger'),
            ],
            'security' => [
                'bearerAuth' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'JWT',
                ],
            ],
        ],
    ],

    'annotations' => [
        'path' => base_path('app/Http/Controllers'),
    ],

    'paths' => [
        'docs' => base_path('public/docs'),
        'yaml' => base_path('app/swagger'),
    ],

    'swagger_ui' => [
        'enabled' => true,
        'path' => '/swagger',
    ],

    'security' => [
        'enabled' => true,
        'path' => base_path('app/Http/Controllers'),
    ],
];
