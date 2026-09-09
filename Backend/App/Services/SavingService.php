<?php

require_once __DIR__ . '/../Repositories/SavingRepository.php';

class SavingService
{
    private SavingRepository $savingRepository;

    public function __construct()
    {
        $this->savingRepository = new SavingRepository();
    }

    public function findUserById(int $userId)
    {
        return $this->savingRepository->findUserById($userId);
    }

    public function createGoal(int $userId, array $data)
    { 
        if (!isset($data['name'], $data['target_amount'], $data['deadline'])) {
            throw new Exception('Dados obrigatórios não informados');
        }

        if (!is_numeric($data['target_amount'])) {
            throw new Exception('O valor deve ser numérico');
        }
        $data['target_amount'] = (float) $data['target_amount'];

        if ($data['target_amount'] <= 0) {
            throw new Exception('O valor deve ser maior que R$0,00');
        }

        $data['user_id'] = $userId;
        $data['current_amount'] = 0;

        return $this->savingRepository->createGoal($data);
    }

    public function updateGoal(int $userId, int $id, array $data)
    {
        if (!isset($data['name'], $data['target_amount'], $data['deadline'])) {
            throw new Exception('Dados obrigatórios não informados');
        }

        if (!is_numeric($data['target_amount'])) {
            throw new Exception('O valor deve ser numérico');
        }

        if ($data['target_amount'] <= 0) {
            throw new Exception('O valor deve ser maior que R$0,00');
        }

        $goal = $this->savingRepository->findById($id);

        if (!$goal) {
            throw new Exception('Meta não encontrada');
        }

        if ((int) $goal['user_id'] !== $userId) {
            throw new Exception('Você não tem permissão para alterar esta meta');
        }

        $data['user_id'] = $userId;
        $data['id'] = $id;
        
        return $this->savingRepository->updateGoal($data);
    }

    public function deleteGoal(int $id, int $userId)
    {
        if ($id <= 0) {
            throw new Exception('Meta inválida');
        }
        if (!isset($userId)) {
            throw new Exception('Usuário não identificado');
        }

        return $this->savingRepository->deleteGoal($id, $userId);


    }

    public function createMovement(int $userId, int $goalId, array $data)
    {
        if (!isset($data['value'], $data['type'], $data['movement_at'])) {
            throw new Exception('Dados obrigatórios não informados');
        }

        if (!is_numeric($data['value'])) {
            throw new Exception('O valor deve ser numérico');
        }

        if ($data['value'] <= 0) {
            throw new Exception('O valor deve ser maior que R$0,00');
        }

        if (!in_array($data['type'], ['deposit', 'withdrawal'], true)) {
            throw new Exception('Tipo de movimentação inválido');
        }

        $goal = $this->savingRepository->findById($goalId);

        if (!$goal) {
            throw new Exception('Meta não encontrada');
        }

        if ((int) $goal['user_id'] !== $userId) {
            throw new Exception(
                'Você não tem permissão para alterar esta meta'
            );
        }

        if ($data['type'] === 'withdrawal' && (float) $data['value'] > (float) $goal['current_amount']) {
            throw new Exception(
                'O valor da retirada é maior que o saldo disponível'
            );
        }

        $data['goal_id'] = $goalId;

        $movementId = $this->savingRepository->createMovement($data);

        $this->savingRepository->updateCurrentAmount($goalId, (float) $data['value'], $data['type']);

        return [
            'id' => $movementId,
            'goal_id' => $goalId,
            'value' => $data['value'],
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'movement_at' => $data['movement_at']
        ];
    }

    public function findMovementsByGoalId(int $userId, int $goalId): ?array {
        
        $goal =$this->savingRepository->findById($goalId);

        if (!$goal) {
            throw new Exception('Meta não encontrada');
        }
        if ((int) $goal['user_id'] !== $userId) {
            throw new Exception('Você não tem permissão para acessar esta meta');
        }

        return $this->savingRepository
            ->findMovementsByGoalId($goalId);
    }
}