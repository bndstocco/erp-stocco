<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Auth;

class UserContext
{
    private static ?UserContext $instance = null;
    private ?int $userId = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function hasUser(): bool
    {
        return $this->userId !== null;
    }

    public function clear(): void
    {
        $this->userId = null;
    }

    private function __clone() {}
    public function __wakeup(): void { throw new \RuntimeException('Cannot unserialize singleton'); }
}
