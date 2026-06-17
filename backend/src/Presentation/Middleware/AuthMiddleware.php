<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Middleware;

use ErpStocco\Application\Services\AuthService;

class AuthMiddleware
{
    private AuthService $authService;

    public function __construct()
    {
        $userRepository = new \ErpStocco\Infrastructure\Repositories\MySQLUserRepository();
        $this->authService = new AuthService($userRepository);
    }

    public function handle(): array
    {
        $token = $this->extractToken();

        if (!$token) {
            $this->unauthorized('Token não fornecido');
        }

        $payload = $this->authService->validateToken($token);

        if (!$payload) {
            $this->unauthorized('Token inválido ou expirado');
        }

        $user = $this->authService->getUserFromToken($token);

        if (!$user || !$user->isActive()) {
            $this->unauthorized('Usuário não encontrado ou inativo');
        }

        return $user->toArray();
    }

    private function extractToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (empty($header)) {
            $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        }

        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function unauthorized(string $message): void
    {
        http_response_code(401);
        echo json_encode([
            'error' => true,
            'message' => $message,
        ]);
        exit;
    }
}
