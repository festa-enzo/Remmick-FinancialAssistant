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
    public function deleteGoal(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM savings_goals
            WHERE id = :id
            AND user_id = :user_id
        ");

        $stmt->execute([
            ':id' => $id,
            ':user_id' => $userId
        ]);

    return $stmt->rowCount() > 0;
    }
    
    public function updateGoal(array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE savings_goals
            SET
                name = :name,
                target_amount = :target_amount,
                deadline = :deadline
            WHERE
                user_id = :user_id
                AND id = :id
        ");

        $stmt->execute([
            ':id' => $data['id'],
            ':user_id' => $data['user_id'],
            ':name' => $data['name'],
            ':target_amount' => $data['target_amount'],
            ':deadline' => $data['deadline']
        ]);

        return $stmt->rowCount() > 0;
    }

    public function createMovement(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO savings_movements (
                goal_id,
                amount,
                type,
                description,
                movement_date
            ) VALUES (
                :goal_id,
                :amount,
                :type,
                :description,
                :movement_date
            )
        ");

        $stmt->execute([
            ':goal_id' => $data['goal_id'],
            ':amount' => $data['value'],
            ':type' => $data['type'],
            ':description' => $data['description'] ?? null,
            ':movement_date' => $data['movement_at']
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateCurrentAmount(int $goalId, float $amount, string $type): bool {
        if ($type === 'deposit') {
            $operation = '+';
        } elseif ($type === 'withdrawal') {
            $operation = '-';
        } else {
            throw new Exception('Tipo de movimentação inválido');
        }

        $stmt = $this->db->prepare("
            UPDATE savings_goals
            SET current_amount = current_amount $operation :amount
            WHERE id = :goal_id
        ");

        $stmt->execute([
            ':amount' => $amount,
            ':goal_id' => $goalId
        ]);

        return $stmt->rowCount() > 0;
    }
    public function findMovementsByGoalId(int $goalId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM savings_movements
            WHERE goal_id = :goal_id
            ORDER BY movement_date DESC, id DESC
        ");

        $stmt->bindValue(
            ':goal_id',
            $goalId,
            PDO::PARAM_INT
        );

        $stmt->execute();

        $rows = $stmt->fetchAll();

    return $rows ?: null;
    }
}