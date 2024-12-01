<?php

return [
    'default' => 'user',
    'documentations' => [
        'user' => [
            'api' => [
                'title' => 'L5 Swagger UI',
            ],
            'routes' => [
                'api' => '/api/documentation/user',
                'docs' => '/api/user/',
                'oauth2_callback' => '/api/user/callback',
            ],
            'paths' => [
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),
                'docs_json' => 'api-docs-v1.json',
                'docs_yaml' => 'api-docs-v1.yaml',
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),
                'annotations' => [
                    base_path('app') . "/Http/Controllers/User",
                    base_path('app/Http/Swagger.php'),
                ],
            ],
        ],
        'admin' => [
            'api' => [
                'title' => 'L5 Swagger UI',
            ],
            'routes' => [
                'api' => '/api/documentation/admin',
                'docs' => '/api/admin/',
                'oauth2_callback' => '/api/admin/callback',
            ],
            'paths' => [
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),
                'docs_json' => 'api-docs-admin-v1.json',
                'docs_yaml' => 'api-docs-admin-v1.yaml',
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),
                'annotations' => [
                    base_path('app') . "/Http/Controllers/Admin",
                    base_path('app/Http/Swagger.php'),
                ],
            ],
        ],
    ],
];
