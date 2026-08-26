<?php

// ====================== ROTAS DA API ======================

$routes = [
    // Auth
    'POST' => [
        '/api/register' => ['AuthController', 'register'],
        '/api/login'    => ['AuthController', 'login'],
        '/api/expense'           => ['ExpenseController', 'createExpense'],
        '/api/income'           => ['IncomeController', 'createIncome'],
        // '/api/logout'   => ['AuthController', 'logout'],
    ],

    'GET' => [
        '/api/expense/month'    => ['ExpenseController', 'findByMonth'],
        '/api/expense/year'    => ['ExpenseController', 'findByYear'],
        '/api/income/month'    => ['IncomeController', 'findByMonth'],
        '/api/income/year'    => ['IncomeController', 'findByYear'],
    ],

    'PUT' => [
        '/api/expense/{id}'  => ['ExpenseController', 'updateExpense'],
        '/api/income/{id}'  => ['IncomeController', 'updateIncome'],
    ],

    'DELETE' => [
        '/api/expense/{id}'      => ['ExpenseController', 'deleteExpense'],
        '/api/income/{id}'      => ['IncomeController', 'deleteIncome'],
    ]
];

return $routes;