<?php

require_once __DIR__ . '/../Repositories/GraphicsRepository.php';

class GraphicsService
{
    private GraphicsRepository $graphicsRepository;

    public function __construct()
    {
        $this->graphicsRepository = new GraphicsRepository();
    }

    public function findFinancialSummary(int $userId, int $year)
    {
        $income = $this->graphicsRepository->findTotalIncome($userId, $year);

        $expense = $this->graphicsRepository->findTotalExpense($userId, $year);

        // pegar o total de income

        // pegar o total de expense

        // calcular o saldo

    //    return [
    //        'total_income' => ...,
    //        'total_expense' => ...,
    //        'balance' => ...
    //    ];
    }
}   