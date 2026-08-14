<?php

require_once __DIR__ . '/../Repositories/AuthRepository';

class AuthService {

    private AuthRepository $authRepository;

    public function __construct() {

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
    }



