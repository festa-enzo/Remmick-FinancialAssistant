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
}