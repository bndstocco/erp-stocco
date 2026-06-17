<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Controllers;

use ErpStocco\Application\Services\AuthService;
use ErpStocco\Application\UseCases\User\AuthenticateUserUseCase;
use ErpStocco\Infrastructure\Repositories\MySQLUserRepository;

class AuthController
{
    private AuthenticateUserUseCase $authenticateUseCase;
    private AuthService $authService;

    public function __construct()
    {
        $userRepository = new MySQLUserRepository();
        $this->authService = new AuthService($userRepository);
        $this->authenticateUseCase = new AuthenticateUserUseCase($this->authService);
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Email e senha são obrigatórios']);
            return;
        }

        try {
            $result = $this->authenticateUseCase->execute($data['email'], $data['password']);
            echo json_encode(['error' => false, 'data' => $result]);
        } catch (\RuntimeException $e) {
            http_response_code(401);
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        }
    }

    public function me(): void
    {
        $auth = new \ErpStocco\Presentation\Middleware\AuthMiddleware();
        $user = $auth->handle();
        echo json_encode(['error' => false, 'data' => $user]);
    }
}
