<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use PDO;

class JWTHandler
{
    private string         $secret;
    private string         $algorithm;
    private int            $accessTtl;
    private int            $refreshTtl;
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $secret = $_ENV['JWT_SECRET'] ?? '';

        if (!is_string($secret) || trim($secret) === '') {
            throw new RuntimeException('JWT_SECRET não está configurado.');
        }

        $this->secret = $secret;
        $this->algorithm  = $_ENV['JWT_ALGORITHM'] ?? 'HS256';
        $this->accessTtl  = (int)($_ENV['JWT_EXPIRATION'] ?? 3600);
        $this->refreshTtl = (int)($_ENV['JWT_REFRESH_EXPIRATION'] ?? 604800);

      $this->pdo = $pdo;
    }

    public function getRefreshTtl(): int
    {
        return $this->refreshTtl;
    }

    public function generateToken(array $user): string
    {
        $now = time();

        $payload = [
            'iss'   => 'agenda-semanal',
            'sub'   => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'iat'   => $now,
            'exp'   => time() + $this->accessTtl
        ];

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function generateRefreshToken(int $user_id): string
    {
        $refreshToken = bin2hex(random_bytes(64));
        $tokenHash = hash('sha256', $refreshToken);

        $expiresAt = date(
            'Y-m-d H:i:s',
            time() + $this->refreshTtl
        );

        $stmt = $this->pdo->prepare("
            INSERT INTO refresh_tokens (user_id, refresh_token, expires_at)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([$user_id, $tokenHash, $expiresAt]);

        return $refreshToken;
    }   

    public function decodeToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode(
                $token,
                new Key($this->secret, $this->algorithm)
            );

            return (array) $decoded;
        } catch (Throwable $e) {
            return null;
        }
    }

}

