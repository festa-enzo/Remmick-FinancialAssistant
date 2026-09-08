<?php

require_once __DIR__ . '/../Repositories/SavingRepository.php';

class SavingService
{
    private SavingRepository $savingRepository;

    public function __construct()
    {
        $this->savingRepository = new SavingRepository();
    }

    public function findByUserId(int $userId)
    {
        return $this->savingRepository->findByUserId($userId);
    }
}