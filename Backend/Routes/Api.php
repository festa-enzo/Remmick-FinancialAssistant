<?php

// ====================== ROTAS DA API ======================

$routes = [
    // Auth
    'POST' => [
        '/api/register' => ['AuthController', 'register'],
        '/api/login'    => ['AuthController', 'login'],
        '/api/expense'           => ['ExpenseController', 'createExpense'],
        '/api/expense/move'      => ['ExpenseController', 'move'],
        // '/api/logout'   => ['AuthController', 'logout'],
    ],

    'GET' => [
        '/api/expense'    => ['ExpenseController', 'index'],
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