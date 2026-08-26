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

        $totalIncome = $income['total'];
        $totalExpense = $expense['total'];
        $balance = $totalIncome - $totalExpense;

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $balance
        ];
    }

    public function findMonthIncome(int $userId, int $year): array
    {
        $monthIncome = [];

        for ($month = 1; $month <= 12; $month++) {

            $result = $this->graphicsRepository->findMonthIncome(
                $userId,
                $month,
                $year
            );

            if ($result !== null) {
                $totalIncome = $result[0]['total'];
            } else{
                $totalIncome = 0;
            }
            $monthIncome[$month] = $totalIncome;
        }

        return $monthIncome;
    }

    public function findMonthExpense(int $userId, int $year): array
    {
        $monthExpense = [];

        for ($month = 1; $month <= 12; $month++) {

            $result = $this->graphicsRepository->findMonthExpense(
                $userId,
                $month,
                $year
            );

            if ($result !== null) {
                $totalExpense = $result[0]['total'];
            } else{
                $totalExpense = 0;
            }
            $monthExpense[$month] = $totalExpense;
        }

        return $monthExpense;
    }
}   