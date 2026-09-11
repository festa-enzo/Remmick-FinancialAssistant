<?php

require_once __DIR__ . '/../Services/ExpenseService.php';
require_once __DIR__ . '/../Core/Response.php';
require_once __DIR__ . '/../Core/Auth.php';

class ExpenseController {

    private ExpenseService $expenseService;

    public function __construct() {
        $this->expenseService = new ExpenseService();
    }

    public function createExpense(){
        try {
            $conteudoBruto = file_get_contents('php://input');
            $data = json_decode($conteudoBruto, true, 512, JSON_THROW_ON_ERROR);
        
            $userId = Auth::requireAuth();
            
            $expense = $this->expenseService->createExpense($userId, $data);

            Response::json([
                'success' => true,
                'data' => $expense
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

    public function updateExpense(int $expenseId){
    try {
        $conteudoBruto = file_get_contents('php://input');
        $data = json_decode($conteudoBruto, true, 512, JSON_THROW_ON_ERROR);

        $userId = Auth::requireAuth();

        $expense = $this->expenseService->updateExpense($expenseId, $userId, $data);

        Response::json([
            'success' => true,
            'data' => $expense
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

    public function deleteExpense(int $expenseId){
    try {
        $userId = Auth::requireAuth();

        $expense = $this->expenseService->deleteExpense($expenseId, $userId);

        Response::json([
            'success' => true,
            'data' => $expense
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
            
            $result = $this->expenseService->findByMonth($userId, $data);

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
            
            $result = $this->expenseService->findByYear($userId, $data);

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

    public function findLatestExpenses() {
        try {
            $data = $_GET;
        
            $userId = Auth::requireAuth();
            
            $result = $this->expenseService->findLatestExpenses($userId);

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