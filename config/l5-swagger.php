<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Racksephnox API',
                'description' => 'Industrial‑grade cryptocurrency investment platform API',
                'version' => '1.0.0',
            ],
            'routes' => [
                'api' => 'api/documentation',
            ],
            'paths' => [
                'use_absolute_path' => true,
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'annotations' => [
                    base_path('app'),
                ],
            ],
        ],
    ],
    'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),
    'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),
    'proxy' => false,
    'additional_config_url' => null,
    'operations_sort' => null,
    'validator_url' => null,
    'constants' => [
        'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://localhost:8000'),
    ],
];
