<?php

require_once __DIR__ . '/../Repositories/AuthRepository.php';
require_once __DIR__ . '/../Core/Jwt.php';
require_once __DIR__ . '/../../Config/Database.php';
use PDO;


class AuthService {

    protected PDO $db;
    private AuthRepository $authRepository;
    private JWTHandler $jwtHandler;

    public function __construct() {
        $this->db = Database::getConnection(); 

        $this->jwtHandler = new JWTHandler($this->db);
        $this->authRepository = new AuthRepository();
    }

    public function register(array $data): array {

        if(!isset($data['name'], $data['email'], $data['password'])){
            throw new Exception('Dados obrigatórios não informados');
        }
        if (strlen($data['password']) < 8 || strlen($data['password']) > 20 || !preg_match('/[0-9]/', $data['password']) || !preg_match('/[A-Z]/', $data['password']) || !preg_match('/[a-z]/', $data['password']) || !preg_match('/[\W_ ]/', $data['password'])){
            throw new InvalidArgumentException("Senha Inválida, certifique-se que sua senha tenha: \n-Entre 8 a 20 caracteres;\n-Uma letra maiúscula e uma minúscula;\n-Um simbolo especial.");
        }
                if (!preg_match('/^[A-Za-zÀ-ÿ ]+$/', $data['name'])){
            throw new InvalidArgumentException("Nome inválido, evite números. \n");
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
            throw new InvalidArgumentException("Email inválido. \n");
        }

        if ($this->authRepository->findByEmail($data['email']) !== null){
            throw new InvalidArgumentException("Email já existente. \n");
        }

        $data['password'] =  password_hash($data['password'], PASSWORD_DEFAULT);
        $user = $this->authRepository->createUser($data);
        unset($user['password']);

        return $user;
        }


    public function updateUser(int $userId, array $data): bool
    {
        if (!isset($data['name'], $data['email'])) {
            throw new Exception('Dados obrigatórios não informados');
        }

        $data['name'] = trim($data['name']);
        $data['email'] = trim($data['email']);

        if (!preg_match('/^[A-Za-zÀ-ÿ ]+$/', $data['name'])) {
            throw new InvalidArgumentException('Nome inválido, evite números.');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email inválido.');
        }

        $existingUser = $this->authRepository->findByEmail($data['email']);

        if ($existingUser !== null && (int) $existingUser['id'] !== $userId) {
            throw new InvalidArgumentException('Email já existente.');
        }

        $data['userId'] = $userId;

        return $this->authRepository->updateUser($userId, $data);
    }
    
    public function updatePassword(int $userId, array $data): bool
    {
        if (empty($data['current_password']) || empty($data['new_password']) || empty($data['confirm_password'])) {
            throw new InvalidArgumentException('Preencha todos os campos de senha.');
        }
        if ($data['new_password'] !== $data['confirm_password']) {
            throw new InvalidArgumentException('A confirmação não corresponde à nova senha.');
        }

        $user = $this->authRepository->findById($userId);

        if (!$user) {
            throw new Exception('Usuário não encontrado.');
        }
        if (!password_verify($data['current_password'], $user['password'])) {
            throw new InvalidArgumentException('Senha atual incorreta.');
        }

        $newPassword = $data['new_password'];

        if (strlen($newPassword) < 8 || strlen($newPassword) > 20 || !preg_match('/[A-Z]/', $newPassword) || !preg_match('/[a-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword) || !preg_match('/[^A-Za-z0-9]/', $newPassword)) {
            throw new InvalidArgumentException('A nova senha deve ter entre 8 e 20 caracteres, incluindo maiúscula, minúscula, número e símbolo.');
        }

        $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        $updated = $this->authRepository->updatePassword($userId, $data);

        if ($updated) {
            $this->authRepository->revokeAllRefreshTokens($userId);
        }
        return $updated;
    }

    public function login(array $data): array {

        if(!isset($data['email'], $data['password'])){
            throw new Exception('Dados obrigatórios não informados');
        }

        $user = $this->authRepository->findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            throw new InvalidArgumentException('E-mail ou senha inválidos');
        }

        $accessToken = $this->jwtHandler->generateToken($user);
        $refreshToken = $this->jwtHandler->generateRefreshToken((int) $user['id']);

        unset($user['password']);

        setcookie('refresh_token', $refreshToken, [
            'expires' => time() + $this->jwtHandler->getRefreshTtl(),
            'path'     => '/',
            'httponly' => true,
            'secure'   => false,
            'samesite' => 'Strict'
        ]);

        return [
            'user' => $user,
            'accessToken' => $accessToken
        ];
    }

    public function logout(): void
    {
        $refreshToken = $_COOKIE['refresh_token'] ?? null;

        if (is_string($refreshToken) && $refreshToken !== '') {
            $this->authRepository->revokeRefreshToken($refreshToken);
        }

        setcookie('refresh_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'secure'   => false, // Deve corresponder ao cookie criado no login.
            'samesite' => 'Strict'
        ]);
    }

    public function deleteUser(int $userId): bool
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Usuário inválido.');
        }

        return $this->authRepository->deleteUser($userId);
    }

    public function getUserProfile(int $userId): ?array
    {
        $user = $this->authRepository->findById($userId);

        if (!$user) {
            return null;
        }

        return [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ];
    }

}



