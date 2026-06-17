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
        $userId = \ErpStocco\Infrastructure\Auth\UserContext::getInstance()->getUserId();
        $user = $this->authService->getUserFromId($userId);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Usuário não encontrado']);
            return;
        }
        echo json_encode(['error' => false, 'data' => $user->toArray()]);
    }

    public function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Nome, e-mail e senha são obrigatórios']);
            return;
        }

        if (strlen($data['password']) < 6) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'A senha deve ter no mínimo 6 caracteres']);
            return;
        }

        try {
            $result = $this->authService->register($data['name'], $data['email'], $data['password']);
            http_response_code(201);
            echo json_encode(['error' => false, 'data' => $result]);
        } catch (\RuntimeException $e) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        }
    }
}
