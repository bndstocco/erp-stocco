<?php

declare(strict_types=1);

namespace ErpStocco\Application\UseCases\User;

use ErpStocco\Application\Services\AuthService;

class AuthenticateUserUseCase
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function execute(string $email, string $password): array
    {
        $result = $this->authService->authenticate($email, $password);

        if (!$result) {
            throw new \RuntimeException('Credenciais inválidas');
        }

        return $result;
    }
}
