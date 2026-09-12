<?php

// ====================== ROTAS DA API ======================

$routes = [
    // Auth
    'POST' => [
        '/api/register' => ['AuthController', 'register'],
        '/api/logout' => ['AuthController', 'logout'],
        '/api/login'    => ['AuthController', 'login'],
        '/api/expense'           => ['ExpenseController', 'createExpense'],
        '/api/income'           => ['IncomeController', 'createIncome'],
        '/api/income/note'           => ['IncomeController', 'createNote'],
        '/api/savings'           => ['SavingController', 'createGoal'],
        '/api/savings/{id}/movements'           => ['SavingController', 'createMovement'],
    ],

    'GET' => [
        '/api/expense/month'    => ['ExpenseController', 'findByMonth'],
        '/api/expense/year'    => ['ExpenseController', 'findByYear'],
        '/api/expense/latest'    => ['ExpenseController', 'findLatestExpenses'],
        '/api/income/month'    => ['IncomeController', 'findByMonth'],
        '/api/income/year'    => ['IncomeController', 'findByYear'],
        '/api/income/note'    => ['IncomeController', 'findNote'],
        '/api/graphics/summary' => ['GraphicsController', 'findFinancialSummary'],
        '/api/graphics/month-income' => ['GraphicsController', 'findMonthIncome'],
        '/api/graphics/month-expense' => ['GraphicsController', 'findMonthExpense'],
        '/api/graphics/expense-category' => ['GraphicsController', 'findExpenseByCategory'],
        '/api/savings' => ['SavingController', 'findUserById'],
        '/api/savings/{id}/movements' => ['SavingController', 'findMovements'],
        '/api/setting/user' => ['AuthController', 'getUserProfile'],
    ],

    'PUT' => [
        '/api/setting/user'  => ['AuthController', 'updateUser'],
        '/api/setting/password' => ['AuthController', 'updatePassword'],
        '/api/expense/{id}'  => ['ExpenseController', 'updateExpense'],
        '/api/income/{id}'  => ['IncomeController', 'updateIncome'],
        '/api/income/note/{id}'  => ['IncomeController', 'updateNote'],
        '/api/savings/{id}'  => ['SavingController', 'updateGoal'],
    ],

    'DELETE' => [
        '/api/expense/{id}'      => ['ExpenseController', 'deleteExpense'],
        '/api/income/{id}'      => ['IncomeController', 'deleteIncome'],
        '/api/savings/{id}'      => ['SavingController', 'deleteGoal'],
        '/api/setting/user' => ['AuthController', 'deleteUser'],
    ]
];

return $routes;