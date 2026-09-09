<?php

require_once __DIR__ . '/../Services/SavingService.php';
require_once __DIR__ . '/../Core/Response.php';
require_once __DIR__ . '/../Core/Auth.php';

class SavingController {

    private SavingService $savingService;

    public function __construct() {
        $this->savingService = new SavingService();
    }

    public function createGoal(){
        try {
            $conteudoBruto = file_get_contents('php://input');
            $data = json_decode($conteudoBruto, true, 512, JSON_THROW_ON_ERROR);
        
            $userId = Auth::requireAuth();
            
            $goal = $this->savingService->createGoal($userId, $data);

            Response::json([
                'success' => true,
                'data' => $goal
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

    public function findUserById() {
        try {        
            $userId = Auth::requireAuth();
            
            $result = $this->savingService->findUserById($userId);

            Response::json([
                'success' => true,
                'data' => $result
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
}