<?php

require_once __DIR__ . '/jwt.php';
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/Response.php';

class Auth
{
    private static ?array $userCache = null;

    /**
     * Retorna o token do header
     */
    private static function getToken(): ?string
    {
        $headers = getallheaders();
        $authorization = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authorization || !preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
            return null;
        }

        return $matches[1];
    }

    /**
     * Retorna os dados do usuário autenticado (com cache)
     */
    public static function user(): array
    {
        if (self::$userCache !== null) {
            return self::$userCache;
        }

        $token = self::getToken();

        if (!$token) {
            Response::json(['success' => false, 'message' => 'Token não informado.'], 401);
        }

        $jwt = new JWTHandler(Database::getConnection());
        $payload = $jwt->decodeToken($token);

        if (!$payload) {
            Response::json(['success' => false, 'message' => 'Token inválido ou expirado.'], 401);
        }

        self::$userCache = $payload;
        return $payload;
    }

    /**
     * Retorna apenas o ID do usuário
     */
    public static function id(): int
    {
        $user = self::user();
        return (int)($user['sub'] ?? 0);
    }

    /**
     * Verifica se está autenticado (sem parar a execução)
     */
    public static function check(): bool
    {
        return self::getToken() !== null;
    }

    /**
     * Verifica autenticação e retorna ID (útil em controllers)
     */
    public static function requireAuth(): int
    {
        return self::id(); // já faz a validação
    }
}