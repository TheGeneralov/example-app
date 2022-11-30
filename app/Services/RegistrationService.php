<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Shop\RegistrationDto;
use App\Exceptions\AlreadyExistsException;
use App\Rep\Shop\RegistrationRepository;
use App\Services\Transactions\TransactionManagerService;
use Exception;

class RegistrationService
{
    private TransactionManagerService $transactionManagerService;
    private RegistrationRepository $registrationRepository;

    public function __construct(
        TransactionManagerService $transactionManagerService,
        RegistrationRepository    $registrationRepository
    ) {
        //$this->transactionManagerService = $transactionManagerService;
        $this->registrationRepository = $registrationRepository;
    }

    /**
     * @throws AlreadyExistsException
     * @throws Exception
     */
    public function registration(RegistrationDto $dto): void
    {
        $isUserExist = $this->registrationRepository->findUser($dto->getEmail());
        if ($isUserExist) {
            throw new AlreadyExistsException('Пользователь уже зарегистрирован');
        }
        $this->transactionManagerService->begin();
        try {
            $this->registrationRepository->createShopAndShopManager($dto);
            $this->transactionManagerService->commit();
        } catch (Exception $e) {
            $this->transactionManagerService->rollback();
            throw $e;
        }
    }
}
