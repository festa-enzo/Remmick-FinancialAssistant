<?php

require_once __DIR__ . '/../Services/GraphicsService.php';
require_once __DIR__ . '/../Core/Response.php';
require_once __DIR__ . '/../Core/Auth.php';

class GraphicsController {

    private GraphicsService $graphicsService;

    public function __construct() {
        $this->graphicsService = new GraphicsService();
    }

    public function findFinancialSummary() {
        try {
            $year = $_GET['year'];
        
            $userId = Auth::requireAuth();
            
            $result = $this->graphicsService->findFinancialSummary($userId, $year);

            Response::json([
                'success' => true,
                'data' => $result
            ], 200);

        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function findMonthIncome() {
        try {
            $year = $_GET['year'];
        
            $userId = Auth::requireAuth();
            
            $result = $this->graphicsService->findMonthIncome($userId, $year);

            Response::json([
                'success' => true,
                'data' => $result
            ], 200);

        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function findMonthExpense() {
        try {
            $year = $_GET['year'];
        
            $userId = Auth::requireAuth();
            
            $result = $this->graphicsService->findMonthExpense($userId, $year);

            Response::json([
                'success' => true,
                'data' => $result
            ], 200);

        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function findExpenseByCategory() {
        try {
            $year = $_GET['year'];
        
            $userId = Auth::requireAuth();
            
            $result = $this->graphicsService->findExpenseByCategory($userId, $year);

            Response::json([
                'success' => true,
                'data' => $result
            ], 200);

        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}