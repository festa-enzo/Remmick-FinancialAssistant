<?php

require_once __DIR__ . '/../Repositories/IncomeRepository.php';

class IncomeService
{
    private IncomeRepository $incomeRepository;

    public function __construct()
    {
        $this->incomeRepository = new IncomeRepository();
    }

    public function findFinancialSummary(int $userId, array $data) 
    {
        $data['user_id'] = $userId;

        if (!isset($data['income_month_id'], $data['income_year'])) {
            throw new Exception('Dados obrigatórios não informados');
        }
        if ($data['income_month_id'] < 1 || $data['income_month_id'] > 12) {
            throw new Exception('O mês deve estar entre 1 e 12');
        }
        if ($data['income_year'] < 2024 || $data['income_year'] > 2035) {
            throw new Exception('O sistema suporta apenas entre 2024 a 2035');
        }


        return $this->incomeRepository->findByMonth($data['user_id'], $data['income_month_id'], $data['income_year']);


    }

    public function findByMonth(int $userId, array $data) 
    {
        $data['user_id'] = $userId;

        if (!isset($data['income_month_id'], $data['income_year'])) {
            throw new Exception('Dados obrigatórios não informados');
        }
        if ($data['income_month_id'] < 1 || $data['income_month_id'] > 12) {
            throw new Exception('O mês deve estar entre 1 e 12');
        }
        if ($data['income_year'] < 2024 || $data['income_year'] > 2035) {
            throw new Exception('O sistema suporta apenas entre 2024 a 2035');
        }


        return $this->incomeRepository->findByMonth($data['user_id'], $data['income_month_id'], $data['income_year']);


    }

    public function findByYear(int $userId, array $data) 
    {
        $data['user_id'] = $userId;

        if (!isset($data['income_year'])) {
            throw new Exception('Dados obrigatórios não informados');
        }
        if ($data['income_year'] < 2024 || $data['income_year'] > 2035) {
            throw new Exception('O sistema suporta apenas entre 2024 a 2035');
        }

        return $this->incomeRepository->findByYear($data['user_id'], $data['income_year']);


    }

    public function createIncome(int $userId, array $data)
    { 
        if (!isset($data['title'], $data['value'], $data['income_month_id'], $data['income_year'], $data['income_origin_id'], $data['income_type_id'])) {
            throw new Exception('Dados obrigatórios não informados');
        }

        if (!is_numeric($data['value'])) {
            throw new Exception('O value deve ser numérico');
        }

        if ($data['value'] <= 0) {
            throw new Exception('O valor deve ser maior que R$0,00');
        }

        if ($data['income_month_id'] < 1 || $data['income_month_id'] > 12) {
            throw new Exception('O mês deve estar entre 1 e 12');
        }

        if ($data['income_year'] < 2024 || $data['income_year'] > 2035) {
            throw new Exception('O sistema suporta apenas entre 2024 a 2035');
        }

        if ($data['income_origin_id'] < 1 || $data['income_origin_id'] > 5) {
            throw new Exception('Não é uma origem válida');
        }  

        if ($data['income_type_id'] < 1 || $data['income_type_id'] > 5) {
            throw new Exception('Não é um tipo válido');
        } 

        $data['user_id'] = $userId;

        return $this->incomeRepository->createIncome($data);
    }

    public function updateIncome(int $incomeId, int $userId, array $data)
    {
        if (!isset($data['title'], $data['value'], $data['income_month_id'], $data['income_year'], $data['income_origin_id'], $data['income_type_id'], $data['is_received'])) {
            throw new Exception('Dados obrigatórios não informados');
        }

        if (!is_numeric($data['value'])) {
            throw new Exception('O value deve ser numérico');
        }

        if ($data['value'] <= 0) {
            throw new Exception('O valor deve ser maior que R$0,00');
        }

        if ($data['income_month_id'] < 1 || $data['income_month_id'] > 12) {
            throw new Exception('O mês deve estar entre 1 e 12');
        }

        if ($data['income_year'] < 2024 || $data['income_year'] > 2035) {
            throw new Exception('O sistema suporta apenas entre 2024 a 2035');
        }

        if ($data['income_origin_id'] < 1 || $data['income_origin_id'] > 5) {
            throw new Exception('Não é uma origem válida');
        }  

        if ($data['income_type_id'] < 1 || $data['income_type_id'] > 5) {
            throw new Exception('Não é um tipo válido');
        } 

        if ($data['is_received'] != 0 && $data['is_received'] != 1) {
           throw new Exception('O status da receita é inválido');
        }

        $data['user_id'] = $userId;
        $data['income_id'] = $incomeId;

        return $this->incomeRepository->updateIncome($data);
    }

    public function deleteIncome(int $incomeId, int $userId)
    {
        if ($incomeId <= 0) {
            throw new Exception('Receita inválida');
        }
        if (!isset($userId)) {
            throw new Exception('Usuário não identificado');
        }

        return $this->incomeRepository->deleteIncome($incomeId, $userId);


    }
//===================== Notas =========================

    public function createNote(int $userId, array $data)
    {
        if (!isset($data['content'])) {
            throw new Exception('A nota está vazia');
        }

        $data['user_id'] = $userId;

        return $this->incomeRepository->createNote($data);
    }

    public function updateNote(int $noteId, int $userId, array $data): bool
    {
        if (!isset($data['content'])) {
            throw new Exception('A nota está vazia');
        }

        $data['user_id'] = $userId;
        $data['note_id'] = $noteId;

        return $this->incomeRepository->updateNote($data);
    }

    public function findNote(int $userId) 
    {
        $data['user_id'] = $userId;

        return $this->incomeRepository->findNote($data['user_id']);
    }

}
