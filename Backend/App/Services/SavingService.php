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
}