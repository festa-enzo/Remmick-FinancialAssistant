<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';
use PDO;

class ExpenseRepository extends BaseRepository
{    
    protected string $table = 'expenses';

    protected array $sortableColumns = ['id', 'value', 'is_paid', 'expense_month', 'expense_year', 'created_at'];

    public function findByMonth(int $user_id, int $expense_month, int $expense_year): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM expenses WHERE user_id = :user_id 
            AND expense_month = :expense_month
            AND expense_year = :expense_year"
        );
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':expense_month', $expense_month, PDO::PARAM_INT);
        $stmt->bindValue(':expense_year', $expense_year, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll();
        return $row ?: null;
    }

//===================== CRUD =========================

    public function createExpense(array $data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO expenses
            (user_id, expense_month, category_id, institution_id, expense_year, title, value, method)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['user_id'],
            $data['expense_month'],
            $data['category_id'],
            $data['institution_id'],
            $data['expense_year'],
            $data['title'],
            $data['value'],
            $data['method'],
        ]);

        $id = (int) $this->db->lastInsertId();

        return $this->findById($id);
    }
    public function deleteExpense(int $expenseId, int $userId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM expenses
            WHERE expense_id = :expense_id
            AND user_id = :user_id
        ");

        $stmt->execute([
            ':expense_id' => $expenseId,
            ':user_id' => $userId
        ]);

    return $stmt->rowCount() > 0;
    }
    
    public function updateExpense(int $expenseId, int $userId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE expenses
            SET
                expense_month = :expense_month,
                category_id = :category_id,
                institution_id = :institution_id,
                expense_year = :expense_year,
                title = :title,
                value = :value,
                method = :method,
                is_paid = :is_paid
            WHERE
                user_id = :user_id
                AND expense_id = :expense_id
        ");

        $stmt->execute([
            ':expense_id' => $expenseId,
            ':user_id' => $userId,
            ':expense_month' => $data['expense_month'],
            ':category_id' => $data['category_id'],
            ':institution_id' => $data['institution_id'],
            ':expense_year' => $data['expense_year'],
            ':title' => $data['title'],
            ':value' => $data['value'],
            ':method' => $data['method'],
            ':is_paid' => $data['is_paid'] 
        ]);

        return $stmt->rowCount() > 0;
    }

//===================== Consultas =========================

    public function findLatestExpenses(int $user_id): array 
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM expenses
            WHERE user_id = :user_id
            ORDER BY created_at DESC
            LIMIT 5        
        ");
        
        $stmt->execute([
        ':user_id' => $user_id
        ]);

        return $stmt->fetchAll();
    }

//===================== Agregações =========================

    public function sumByMonth(int $user_id, int $expense_month, int $expense_year): array
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(value), 0) AS total
            FROM expenses
            WHERE user_id = :user_id
                AND expense_month = :expense_month
                AND expense_year = :expense_year
        ");

        $stmt->execute([
            ':user_id' => $user_id,
            ':expense_month' => $expense_month,
            ':expense_year' => $expense_year
        ]);

        return $stmt->fetch();

    }





}
