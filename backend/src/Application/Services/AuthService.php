<?php

declare(strict_types=1);

namespace ErpStocco\Application\Services;

use ErpStocco\Domain\Entities\User;
use ErpStocco\Domain\Repositories\UserRepositoryInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService
{
    private UserRepositoryInterface $userRepository;
    private string $jwtSecret;
    private int $jwtExpiry;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'default_secret';
        $this->jwtExpiry = (int) ($_ENV['JWT_EXPIRY'] ?? 86400);
    }

    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !$user->isActive()) {
            return null;
        }

        if (!password_verify($password, $user->getPassword())) {
            return null;
        }

        $token = $this->generateToken($user);

        return [
            'token' => $token,
            'user' => $user->toArray(),
        ];
    }

    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getUserFromToken(string $token): ?User
    {
        $payload = $this->validateToken($token);
        if (!$payload || !isset($payload['sub'])) {
            return null;
        }

        return $this->userRepository->findById((int) $payload['sub']);
    }

    public function getUserFromId(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function register(string $name, string $email, string $password): array
    {
        $existing = $this->userRepository->findByEmail($email);
        if ($existing) {
            throw new \RuntimeException('Este e-mail já está cadastrado');
        }

        $user = new User(
            name: $name,
            email: new \ErpStocco\Domain\ValueObjects\Email($email),
            password: $this->hashPassword($password),
        );

        $user = $this->userRepository->save($user);
        $token = $this->generateToken($user);

        return [
            'token' => $token,
            'user' => $user->toArray(),
        ];
    }

    public function generateToken(User $user): string
    {
        $payload = [
            'iss' => $_ENV['APP_URL'] ?? 'http://localhost:8000',
            'sub' => $user->getId(),
            'iat' => time(),
            'exp' => time() + $this->jwtExpiry,
            'name' => $user->getName(),
            'email' => $user->getEmail()->value(),
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }
}
