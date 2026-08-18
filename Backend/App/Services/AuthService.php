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

    public function login(array $data): array {

        if(!isset($data['email'], $data['password'])){
            throw new Exception('Dados obrigatórios não informados');
        }

        $user = $this->authRepository->findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            throw new InvalidArgumentException('E-mail ou senha inválidos');
        }

        $accessToken = $this->jwtHandler->generateToken($user);
        $refreshToken = $this->jwtHandler->generateRefreshToken($user['id']);

        setcookie('refresh_token', $refreshToken, [
            'expires'  => time() + $_ENV['JWT_REFRESH_EXPIRATION'], // 7 dias
            'path'     => '/',
            'httponly' => true,
            'secure'   => false,
            'samesite' => 'Strict'
        ]);

        unset($user['password']);

        return [
            'user' => $user,
            'accessToken' => $accessToken
        ];
    }
}



