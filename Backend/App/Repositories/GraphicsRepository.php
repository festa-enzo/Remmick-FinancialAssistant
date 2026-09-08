<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';
use PDO;

class GraphicsRepository extends BaseRepository
{    
        protected string $table = 'expenses';

    protected array $sortableColumns = ['id', 'value', 'is_paid', 'expense_month_id', 'expense_year', 'created_at'];

    public function findTotalIncome(int $user_id, int $income_year): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(value), 0) AS total
            FROM incomes
            WHERE user_id = :user_id
            AND income_year = :income_year"
        );
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':income_year', $income_year, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findTotalExpense(int $user_id, int $expense_year): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(value), 0) AS total
            FROM expenses
            WHERE user_id = :user_id
            AND expense_year = :expense_year"
        );
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':expense_year', $expense_year, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findMonthIncome(int $user_id, int $income_month_id, int $income_year): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT
                income_month_id,
                SUM(value) AS total
            FROM incomes
            WHERE user_id = :user_id
            AND income_year = :income_year
            AND income_month_id = :income_month_id
            GROUP BY income_month_id
            ORDER BY income_month_id"
        );
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':income_year', $income_year, PDO::PARAM_INT);
        $stmt->bindValue(':income_month_id', $income_month_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll();
        return $row ?: null;
    }

    public function findMonthExpense(int $user_id, int $expense_month_id, int $expense_year): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT
                expense_month_id,
                SUM(value) AS total
            FROM expenses
            WHERE user_id = :user_id
            AND expense_year = :expense_year
            AND expense_month_id = :expense_month_id
            GROUP BY expense_month_id
            ORDER BY expense_month_id"
        );
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':expense_year', $expense_year, PDO::PARAM_INT);
        $stmt->bindValue(':expense_month_id', $expense_month_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll();
        return $row ?: null;
    }

    public function findExpenseByCategory(int $user_id, int $expense_year)
    {
        $stmt = $this->db->prepare(
            "SELECT
                category_id,
                SUM(value) AS total
            FROM expenses
            WHERE user_id = :user_id
            AND expense_year = :expense_year
            GROUP BY category_id
            ORDER BY category_id"
        );
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':expense_year', $expense_year, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll();
        return $row ?: null;
    }

}