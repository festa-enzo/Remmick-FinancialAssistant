<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';
use PDO;

class SavingRepository extends BaseRepository
{    
    protected string $table = 'saving_goals';

    protected array $sortableColumns = ['id', 'name', 'deadline', 'target_amount'];

    public function findByUserId(int $user_id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM saving_goals 
            WHERE user_id = :user_id
            ORDER BY created_at DESC"
        );
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll();
        return $row ?: null;
    }
}