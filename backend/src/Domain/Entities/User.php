<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Entities;

use ErpStocco\Domain\ValueObjects\Email;

class User
{
    private ?int $id;
    private string $name;
    private Email $email;
    private string $password;
    private ?string $phone;
    private ?string $avatar;
    private ?int $roleId;
    private string $status;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id = null,
        string $name = '',
        ?Email $email = null,
        string $password = '',
        ?string $phone = null,
        ?string $avatar = null,
        ?int $roleId = null,
        string $status = 'active',
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email ?? new Email('');
        $this->password = $password;
        $this->phone = $phone;
        $this->avatar = $avatar;
        $this->roleId = $roleId;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): Email { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getPhone(): ?string { return $this->phone; }
    public function getAvatar(): ?string { return $this->avatar; }
    public function getRoleId(): ?int { return $this->roleId; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = $name; }
    public function setEmail(Email $email): void { $this->email = $email; }
    public function setPassword(string $password): void { $this->password = $password; }
    public function setPhone(?string $phone): void { $this->phone = $phone; }
    public function setAvatar(?string $avatar): void { $this->avatar = $avatar; }
    public function setRoleId(?int $roleId): void { $this->roleId = $roleId; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function isActive(): bool { return $this->status === 'active'; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email->value(),
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'role_id' => $this->roleId,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
