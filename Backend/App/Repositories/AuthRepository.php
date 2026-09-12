<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';

class AuthRepository extends BaseRepository
{
    protected string $table = 'users';

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
        return $this->create($data);
    }

    public function updateUser(int $userId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET
                name = :name,
                email = :email
            WHERE
                id = :id
        ");

        $stmt->execute([
            ':id' => $data['userId'],
            ':name' => $data['name'],
            ':email' => $data['email']
        ]);

        return $stmt->rowCount() > 0;
    }

    public function updatePassword(int $userId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET
                password = :password
            WHERE
                id = :id
        ");

        $stmt->execute([
            ':id' => $userId,
            ':password' => $data['password']
        ]);

        return $stmt->rowCount() > 0;
    }

    public function findRefreshToken(string $token): ?array
    {
        $tokenHash = hash('sha256', $token);

        $stmt = $this->db->prepare("
            SELECT rt.*, u.id AS user_id, u.name, u.email
            FROM refresh_tokens rt
            JOIN users u ON rt.user_id = u.id
            WHERE rt.refresh_token = :token_hash
            AND rt.expires_at > NOW()
            LIMIT 1
        ");

        $stmt->execute([
            ':token_hash' => $tokenHash
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function revokeRefreshToken(string $token): void
    {
        $tokenHash = hash('sha256', $token);

        $stmt = $this->db->prepare("
            DELETE FROM refresh_tokens
            WHERE refresh_token = :token_hash
        ");

        $stmt->execute([
            ':token_hash' => $tokenHash
        ]);
    }

    public function revokeAllRefreshTokens(int $userId): void
    {
        $stmt = $this->db->prepare("
            DELETE FROM refresh_tokens
            WHERE user_id = :user_id
        ");

        $stmt->execute([
            ':user_id' => $userId
        ]);
    }

    public function deleteUser(int $userId): bool
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("DELETE FROM refresh_tokens WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $userId]);

            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $userId]);

            $deleted = $stmt->rowCount() > 0;

            if (!$deleted) {
                $this->db->rollBack();
                return false;
            }
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}
