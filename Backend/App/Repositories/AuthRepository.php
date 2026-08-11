<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';
use PDO;

class AuthRepository extends BaseRepository
{
    protected string $table = 'users';

    protected array $sortableColumns = ['id', 'name', 'email', 'created_at'];

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE email = :email LIMIT 1"
        );
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function createUser(array $data): array
    {
        $data['password']   = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->create($data);
    }

    public function saveRefreshToken(int $userId, string $token, string $expiresAt): void
    {
        // Remove tokens antigos do user
        $this->db->prepare(
            "DELETE FROM refresh_tokens WHERE user_id = :user_id"
        )->execute([':user_id' => $userId]);

        $stmt = $this->db->prepare(
            "INSERT INTO refresh_tokens (user_id, refresh_token, expires_at, created_at)
             VALUES (:user_id, :token, :expires_at, NOW())"
        );
        $stmt->execute([
            ':user_id'    => $userId,
            ':token'      => $token,
            ':expires_at' => $expiresAt,
        ]);
    }

    public function findRefreshToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT rt.*, u.id AS user_id, u.email
             FROM refresh_tokens rt
             JOIN users u ON rt.user_id = u.id
             WHERE rt.refresh_token = :token AND rt.expires_at > NOW()
             LIMIT 1"
        );
        $stmt->bindValue(':token', $token);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function revokeRefreshToken(string $token): void
    {
        $this->db->prepare(
            "DELETE FROM refresh_tokens WHERE refresh_token = :token"
        )->execute([':token' => $token]);
    }
}
