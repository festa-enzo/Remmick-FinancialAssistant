<?php

require_once __DIR__ . '/../../Services/AuthService.php';
require_once __DIR__ . '/../../Core/Response.php';
require_once __DIR__ . '/../../Core/Auth.php';

class AuthController {

    private AuthService $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function register(){
        try {
            $conteudoBruto = file_get_contents('php://input');
            $data = json_decode($conteudoBruto, true, 512, JSON_THROW_ON_ERROR);
                
            $user = $this->authService->register($data);

            Response::json([
                'success' => true,
                'data' => $user
            ], 201);

        } catch (JsonException $e) {
            Response::json([
                'success' => false,
                'message' => 'JSON inválido'
            ], 400);

        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

    }
}