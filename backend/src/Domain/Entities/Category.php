<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Entities;

class Category
{
    private ?int $id;
    private string $name;
    private ?string $description;
    private ?int $parentId;
    private string $status;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id = null,
        string $name = '',
        ?string $description = null,
        ?int $parentId = null,
        string $status = 'active',
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->parentId = $parentId;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDescription(): ?string { return $this->description; }
    public function getParentId(): ?int { return $this->parentId; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = $name; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setParentId(?int $parentId): void { $this->parentId = $parentId; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function isActive(): bool { return $this->status === 'active'; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parentId,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
