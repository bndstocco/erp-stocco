<?php

declare(strict_types=1);

namespace ErpStocco\Application\DTOs;

class UserDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly ?string $avatar,
        public readonly ?int $roleId,
        public readonly ?string $roleName,
        public readonly string $status,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? '',
            email: $data['email'] ?? '',
            phone: $data['phone'] ?? null,
            avatar: $data['avatar'] ?? null,
            roleId: $data['role_id'] ?? null,
            roleName: $data['role_name'] ?? null,
            status: $data['status'] ?? 'active',
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'role_id' => $this->roleId,
            'role_name' => $this->roleName,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
