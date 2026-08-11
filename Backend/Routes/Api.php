<?php

// ====================== ROTAS DA API ======================

$routes = [
    // Auth
    'POST' => [
        '/api/register' => ['AuthController', 'register'],
        '/api/login'    => ['AuthController', 'login'],
        '/api/tasks'           => ['TaskController', 'create'],
        '/api/tasks/move'      => ['TaskController', 'move'],
        // '/api/logout'   => ['AuthController', 'logout'],
    ],

    'GET' => [
        '/api/tasks'    => ['TaskController', 'index'],
        '/api/columns'  => ['ColumnController', 'index'],
    ],

    'PUT' => [
        '/api/tasks/{id}'      => ['TaskController', 'update'],
    ],

    'DELETE' => [
        '/api/tasks/{id}'      => ['TaskController', 'delete'],
    ]
];

return $routes;