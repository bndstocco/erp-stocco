<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Entities;

class Account
{
    private ?int $id;
    private string $name;
    private string $type;
    private float $balance;
    private ?string $bank;
    private ?string $agency;
    private ?string $accountNumber;
    private ?string $description;
    private string $status;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id = null,
        string $name = '',
        string $type = 'checking',
        float $balance = 0.0,
        ?string $bank = null,
        ?string $agency = null,
        ?string $accountNumber = null,
        ?string $description = null,
        string $status = 'active',
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->balance = $balance;
        $this->bank = $bank;
        $this->agency = $agency;
        $this->accountNumber = $accountNumber;
        $this->description = $description;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getType(): string { return $this->type; }
    public function getBalance(): float { return $this->balance; }
    public function getBank(): ?string { return $this->bank; }
    public function getAgency(): ?string { return $this->agency; }
    public function getAccountNumber(): ?string { return $this->accountNumber; }
    public function getDescription(): ?string { return $this->description; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = $name; }
    public function setType(string $type): void { $this->type = $type; }
    public function setBalance(float $balance): void { $this->balance = $balance; }
    public function setBank(?string $bank): void { $this->bank = $bank; }
    public function setAgency(?string $agency): void { $this->agency = $agency; }
    public function setAccountNumber(?string $accountNumber): void { $this->accountNumber = $accountNumber; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function credit(float $amount): void { $this->balance += $amount; }
    public function debit(float $amount): void { $this->balance -= $amount; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'balance' => $this->balance,
            'bank' => $this->bank,
            'agency' => $this->agency,
            'account_number' => $this->accountNumber,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
