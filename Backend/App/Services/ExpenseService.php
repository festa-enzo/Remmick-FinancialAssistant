<?php

require_once __DIR__ . '/../Repositories/ExpenseRepository.php';

class ExpenseService
{
    private ExpenseRepository $expenseRepository;

    public function __construct()
    {
        $this->expenseRepository = new ExpenseRepository();
    }

    public function createExpense(int $userId, array $data)
    { 
        if (!isset($data['title'], $data['value'], $data['expense_month_id'], $data['expense_year'], $data['category_id'], $data['institution_id'], $data['method_id'])) {
            throw new Exception('Dados obrigatórios não informados');
        }

        if (!is_numeric($data['value'])) {
            throw new Exception('O value deve ser numérico');
        }

        if ($data['value'] <= 0) {
            throw new Exception('O valor deve ser maior que R$0,00');
        }

        if ($data['expense_month_id'] < 1 || $data['expense_month_id'] > 12) {
            throw new Exception('O mês deve estar entre 1 e 12');
        }

        if ($data['expense_year'] < 2024 || $data['expense_year'] > 2035) {
            throw new Exception('O sistema suporta apenas entre 2024 a 2035');
        }

        if ($data['category_id'] < 1 || $data['category_id'] > 12) {
            throw new Exception('Não é uma categoria válida');
        }  // Alimentação, Transporte, Moradia, Compras, lazer, Saúde, Educação, Tecnologia, Serviços, Vestuário, Presente, Outros

        if ($data['institution_id'] < 1 || $data['institution_id'] > 15) {
            throw new Exception('Não é uma intituição válida');
        } // Nubank, Itaú, Bradesco, Banco do Brasil, Caixa, Santander, Inter, C6, BTG, XP, Mercado Pago, Pagbank, Neon, Picpay, Sicredi 

        if ($data['method_id'] < 1 || $data['method_id'] > 4) {
            throw new Exception('Não é um método válido');
        } // dinheiro, crédito, débito, boleto.

        $data['user_id'] = $userId;

        return $this->expenseRepository->createExpense($data);
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