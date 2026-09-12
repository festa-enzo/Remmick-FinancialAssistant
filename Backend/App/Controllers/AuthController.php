<?php

require_once __DIR__ . '/../Services/AuthService.php';
require_once __DIR__ . '/../Core/Response.php';
require_once __DIR__ . '/../Core/Auth.php';

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

    public function login() {
        try {
            $conteudoBruto = file_get_contents('php://input');
            $data = json_decode($conteudoBruto, true, 512, JSON_THROW_ON_ERROR);

            $result = $this->authService->login($data);

            Response::json([
                'success'     => true,
                'accessToken' => $result['accessToken'],
                'user'        => [
                    'id'   => $result['user']['id'],
                    'name' => $result['user']['name']
                ]
            ]);

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

    public function logout(){
        try {
            Auth::requireAuth();

            $this->authService->logout();

            Response::json([
                'success' => true,
                'message' => 'Logout realizado com sucesso.'
            ], 200);

        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function updateUser(){
        try {
            $conteudoBruto = file_get_contents('php://input');
            $data = json_decode($conteudoBruto, true, 512, JSON_THROW_ON_ERROR);

            $userId = Auth::requireAuth();

            $updateUser = $this->authService->updateUser($userId, $data);

            Response::json([
                'success' => true,
                'data' => $updateUser
            ], 200);

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

    public function updatePassword(){
        try {
            $conteudoBruto = file_get_contents('php://input');
            $data = json_decode($conteudoBruto, true, 512, JSON_THROW_ON_ERROR);

            $userId = Auth::requireAuth();
            $updatePassword = $this->authService->updatePassword($userId, $data);

            if (!$updatePassword) {
                Response::json([
                    'success' => false,
                    'message' => 'Não foi possível atualizar a senha.'
                ], 500);
                return;
            }

            Response::json([
                'success' => true,
                'data' => $updatePassword
            ], 200);

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

    public function deleteUser()
    {
        try {
            $userId = Auth::requireAuth();

            $deleted = $this->authService->deleteUser($userId);

            if (!$deleted) {
                Response::json([
                    'success' => false,
                    'message' => 'Não foi possível excluir a conta.'
                ], 404);
                return;
            }

            setcookie('refresh_token', '', [
                'expires'  => time() - 3600,
                'path'     => '/',
                'httponly' => true,
                'secure'   => false,
                'samesite' => 'Strict'
            ]);

            Response::json([
                'success' => true,
                'message' => 'Conta excluída com sucesso.'
            ], 200);

        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getUserProfile(): void
    {
        try {
            $userId = Auth::requireAuth();

            $user = $this->authService->getUserProfile($userId);

            if ($user === null) {
                Response::json([
                    'success' => false,
                    'message' => 'Usuário não encontrado.'
                ], 404);
                return;
            }

            Response::json([
                'success' => true,
                'data' => $user
            ], 200);

        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}