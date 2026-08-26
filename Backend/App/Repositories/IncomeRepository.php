<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';
use PDO;

class IncomeRepository extends BaseRepository
{    
    protected string $table = 'incomes';

    protected array $sortableColumns = ['id', 'value', 'is_received', 'income_month_id', 'income_year', 'created_at', 'updated_at'];

    public function findByMonth(int $user_id, int $income_month_id, int $income_year): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM incomes WHERE user_id = :user_id 
            AND income_month_id = :income_month_id
            AND income_year = :income_year"
        );
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':income_month_id', $income_month_id, PDO::PARAM_INT);
        $stmt->bindValue(':income_year', $income_year, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll();
        return $row ?: null;
    }

    public function findByYear(int $user_id, int $income_year): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM incomes WHERE user_id = :user_id 
            AND income_year = :income_year"
        );
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':income_year', $income_year, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll();
        return $row ?: null;
    }

//===================== CRUD =========================

    public function createIncome(array $data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO incomes
            (user_id, value, income_origin_id, income_month_id, income_year, income_type_id, title)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['user_id'],
            $data['value'],
            $data['income_origin_id'],
            $data['income_month_id'],
            $data['income_year'],
            $data['income_type_id'],
            $data['title'],
        ]);

        $id = (int) $this->db->lastInsertId();

        return $this->findById($id);
    }
    
    public function deleteIncome(int $incomeId, int $userId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM incomes
            WHERE id = :id
            AND user_id = :user_id
        ");

        $stmt->execute([
            ':id' => $incomeId,
            ':user_id' => $userId
        ]);

    return $stmt->rowCount() > 0;
    }
    
    public function updateIncome(array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE incomes
            SET
                value = :value,
                income_origin_id = :income_origin_id,
                income_month_id = :income_month_id,
                income_year = :income_year,
                is_received = :is_received,
                income_type_id = :income_type_id,
                title = :title
            WHERE
                user_id = :user_id
                AND id = :id
        ");

        $stmt->execute([
            ':id' => $data['income_id'],
            ':user_id' => $data['user_id'],
            ':value' => $data['value'],
            ':income_origin_id' => $data['income_origin_id'],
            ':income_month_id' => $data['income_month_id'],
            ':income_year' => $data['income_year'],
            ':is_received' => $data['is_received'],
            ':income_type_id' => $data['income_type_id'],
            ':title' => $data['title'] 
        ]);

        return $stmt->rowCount() > 0;
    }

//===================== Consultas =========================

    public function findLatestIncomes(int $user_id): array 
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM incomes
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

    public function sumByMonth(int $user_id, int $income_month_id, int $income_year): array
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(value), 0) AS total
            FROM incomes
            WHERE user_id = :user_id
                AND income_month_id = :income_month_id
                AND income_year = :income_year
        ");

        $stmt->execute([
            ':user_id' => $user_id,
            ':income_month_id' => $income_month_id,
            ':income_year' => $income_year
        ]);

        return $stmt->fetch();

    }

//===================== Notas =========================

    public function createNote(array $data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO notes
            (user_id, content)
            VALUES (?, ?)
        ");

        $stmt->execute([
            $data['user_id'],
            $data['content'],
        ]);

        return (int) $this->db->lastInsertId();
    }


    public function updateNote(array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE notes
            SET
                content = :content
            WHERE
                user_id = :user_id
                AND id = :id
        ");

        $stmt->execute([
            ':id' => $data['note_id'],
            ':user_id' => $data['user_id'],
            ':content' => $data['content'],
        ]);

        return $stmt->rowCount() > 0;
    }

    public function findNote(int $user_id): ?array 
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM notes
            WHERE user_id = :user_id        
        ");
        
        $stmt->execute([
        ':user_id' => $user_id
        ]);

        return $stmt->fetch() ?: null;
    }


}
