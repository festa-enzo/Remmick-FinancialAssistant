<?php

require_once __DIR__ . '/../Services/IncomeService.php';
require_once __DIR__ . '/../Core/Response.php';
require_once __DIR__ . '/../Core/Auth.php';

class IncomeController {

    private IncomeService $incomeService;

    public function __construct() {
        $this->incomeService = new IncomeService();
    }

    public function createIncome(){
        try {
            $conteudoBruto = file_get_contents('php://input');
            $data = json_decode($conteudoBruto, true, 512, JSON_THROW_ON_ERROR);
        
            $userId = Auth::requireAuth();
            
            $income = $this->incomeService->createIncome($userId, $data);

            Response::json([
                'success' => true,
                'data' => $income
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

    public function updateIncome(int $incomeId){
    try {
        $conteudoBruto = file_get_contents('php://input');
        $data = json_decode($conteudoBruto, true, 512, JSON_THROW_ON_ERROR);

        $userId = Auth::requireAuth();

        $income = $this->incomeService->updateIncome($incomeId, $userId, $data);

        Response::json([
            'success' => true,
            'data' => $income
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

    public function deleteIncome(int $incomeId){
    try {
        $userId = Auth::requireAuth();

        $income = $this->incomeService->deleteIncome($incomeId, $userId);

        Response::json([
            'success' => true,
            'data' => $income
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

    public function findByMonth() {
        try {
            $data = $_GET;
        
            $userId = Auth::requireAuth();
            
            $result = $this->incomeService->findByMonth($userId, $data);

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
    public function findByYear() {
        try {
            $data = $_GET;
        
            $userId = Auth::requireAuth();
            
            $result = $this->incomeService->findByYear($userId, $data);

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

        public function createNote(){
        try {
            $conteudoBruto = file_get_contents('php://input');
            $data = json_decode($conteudoBruto, true, 512, JSON_THROW_ON_ERROR);
        
            $userId = Auth::requireAuth();
            
            $income = $this->incomeService->createNote($userId, $data);

            Response::json([
                'success' => true,
                'data' => $income
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

    public function updateNote(int $noteId){
    try {
        $conteudoBruto = file_get_contents('php://input');
        $data = json_decode($conteudoBruto, true, 512, JSON_THROW_ON_ERROR);

        $userId = Auth::requireAuth();

        $income = $this->incomeService->updateNote($noteId, $userId, $data);

        Response::json([
            'success' => true,
            'data' => $income
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
    public function findNote() {
        try {        
            $userId = Auth::requireAuth();
            
            $result = $this->incomeService->findNote($userId);

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