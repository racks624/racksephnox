<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Racksephnox Divine Crypto API',
                'description' => 'Industrial-grade cryptocurrency investment platform with RX Machine Series, Trading, and 8888 Hz Wealth Frequency',
                'version' => '2.0.0',
                'contact' => [
                    'email' => 'api@racksephnox.com',
                    'name' => 'Racksephnox API Support',
                ],
                'license' => [
                    'name' => 'Proprietary',
                    'url' => 'https://racksephnox.com/license',
                ],
            ],
            'routes' => [
                'api' => 'api/v1',
                'docs' => 'api-docs',
            ],
            'paths' => [
                'use_absolute_path' => false,
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'annotations' => [
                    base_path('app/Http/Controllers/Api'),
                ],
                'swagger_ui_assets_path' => storage_path('api-docs'),
            ],
            'swagger_ui' => [
                'display_operation_id' => false,
                'display_request_duration' => true,
                'default_models_expand_depth' => 1,
                'default_model_expand_depth' => 1,
                'default_model_rendering' => 'model',
                'doc_expansion' => 'list',
                'filter' => true,
                'operations_sorter' => 'alpha',
                'show_common_extensions' => false,
                'show_extensions' => false,
                'tags_sorter' => 'alpha',
                'try_it_out_enabled' => true,
                'persist_authorization' => true,
            ],
        ],
    ],
    'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', true),
    'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),
    'proxy' => false,
    'additional_config_url' => null,
    'operations_sort' => null,
    'validator_url' => null,
    'constants' => [
        'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', env('APP_URL', 'http://localhost:8000')),
    ],
];
