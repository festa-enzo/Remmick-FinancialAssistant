<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';
use PDO;

class SavingRepository extends BaseRepository
{    
    protected string $table = 'savings_goals';

    protected array $sortableColumns = ['id', 'name', 'deadline', 'target_amount'];

    public function findUserById(int $user_id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM savings_goals 
            WHERE user_id = :user_id
            ORDER BY created_at DESC"
        );
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll();
        return $row ?: null;
    }


//===================== CRUD =========================

    public function createGoal(array $data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO savings_goals
            (user_id, name, target_amount, current_amount, deadline)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['user_id'],
            $data['name'],
            $data['target_amount'],
            $data['current_amount'],
            $data['deadline'],
        ]);

        $id = (int) $this->db->lastInsertId();

        return $this->findById($id);
    }
}