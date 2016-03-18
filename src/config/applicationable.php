<?php
return [
    'middleware' => ['oauth'],
    'routes' => [
        'prefix' => 'api/v1',
        'project_name' => '/projects',
        'consumer_name' => '/checker',
    ],
    'scopes' => [
        'scope1',
        'scope2',
        'scope3',
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
