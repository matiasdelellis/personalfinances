<?php
return [
    'resources' => [
        'account' => ['url' => '/accounts'],
        'transaction' => ['url' => '/transactions'],
        'account_api' => ['url' => '/api/0.1/accounts']
    ],
    'routes' => [
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'transaction#findAllAccount', 'url' => '/transactions/{account}', 'verb' => 'GET'],
        ['name' => 'account#balance', 'url' => '/balance/{id}', 'verb' => 'GET'],
        ['name' => 'account_api#preflighted_cors', 'url' => '/api/0.1/{path}',
         'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']]
    ]
];