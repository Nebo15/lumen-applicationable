<?php
return [
    'middleware' => 'oauth',
    'routes' => [
        'prefix' => 'api/v1',
        'project_name' => '/projects',
        'consumer_name' => '/projects/consumer',
    ],
    'scopes' => [
        'create',
        'read',
        'update',
        'delete',
        'check',
    ]
];
