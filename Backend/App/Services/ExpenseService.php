<?php

require_once __DIR__ . '/../Repositories/ExpenseRepository.php';

class ExpenseService
{
    private ExpenseRepository $expenseRepository;

    public function __construct()
    {
        $this->ExpenseRepository = new ExpenseRepository();
    }

    public function createExpense(int $userId, array $data)
    { 
        if (!isset($data['title'], $data['value'], $data['expense_month'], $data['expense_year'] $data['category_id'], $data['institution_id'], $data['method']))
            throw new Exception('Dados obrigatórios não informados');
        }

        if ($data['value'] =< 0) {
            throw new Exception('O valor deve ser maior que R$0,00');
        }

        if (!$data['expense_month'] => 1 || !$data['expense_month'] =<12) {
            throw new Exception('O valor deve ser maior que R$0,00');
        }

        // Validar os dados

        // Preparar os dados necessários

        // Chamar o repository
    }

    public function updateExpense(int $expenseId, int $userId, array $data)
    {
        // Validar os dados

        // Chamar o repository
    }

    public function deleteExpense(int $expenseId, int $userId)
    {
        // Chamar o repository
    }
}