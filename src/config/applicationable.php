<?php
return [
    'middleware' => ['oauth'],
    'routes' => [
        'prefix' => 'api/v1',
        'applications' => '/projects',
        'consumers' => '/checker',
        'users' => '/checker',
    ],
    'scopes' => [
        'users' => [
            'scope1',
            'scope2',
            'scope3',
        ],
        'consumers' => [
            'scope1',
            'scope2',
        ],
    ],
    'acl' => [
        'get' => [
            '~^api/v1/(.+)$~' => ['scope1', 'scope3'],
        ],
        'post' => [],
        'put' => [],
        'delete' => [],
    ],
];
