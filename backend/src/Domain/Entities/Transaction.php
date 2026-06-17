<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Entities;

class Transaction
{
    private ?int $id;
    private int $accountId;
    private string $type;
    private ?string $category;
    private float $amount;
    private ?string $description;
    private ?int $destinationAccountId;
    private string $paymentMethod;
    private string $status;
    private string $transactionDate;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id = null,
        int $accountId = 0,
        string $type = 'income',
        ?string $category = null,
        float $amount = 0.0,
        ?string $description = null,
        ?int $destinationAccountId = null,
        string $paymentMethod = 'cash',
        string $status = 'completed',
        string $transactionDate = '',
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->accountId = $accountId;
        $this->type = $type;
        $this->category = $category;
        $this->amount = $amount;
        $this->description = $description;
        $this->destinationAccountId = $destinationAccountId;
        $this->paymentMethod = $paymentMethod;
        $this->status = $status;
        $this->transactionDate = $transactionDate;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getAccountId(): int { return $this->accountId; }
    public function getType(): string { return $this->type; }
    public function getCategory(): ?string { return $this->category; }
    public function getAmount(): float { return $this->amount; }
    public function getDescription(): ?string { return $this->description; }
    public function getDestinationAccountId(): ?int { return $this->destinationAccountId; }
    public function getPaymentMethod(): string { return $this->paymentMethod; }
    public function getStatus(): string { return $this->status; }
    public function getTransactionDate(): string { return $this->transactionDate; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    public function setId(int $id): void { $this->id = $id; }
    public function setAccountId(int $accountId): void { $this->accountId = $accountId; }
    public function setType(string $type): void { $this->type = $type; }
    public function setCategory(?string $category): void { $this->category = $category; }
    public function setAmount(float $amount): void { $this->amount = $amount; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setDestinationAccountId(?int $destinationAccountId): void { $this->destinationAccountId = $destinationAccountId; }
    public function setPaymentMethod(string $paymentMethod): void { $this->paymentMethod = $paymentMethod; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setTransactionDate(string $transactionDate): void { $this->transactionDate = $transactionDate; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'account_id' => $this->accountId,
            'type' => $this->type,
            'category' => $this->category,
            'amount' => $this->amount,
            'description' => $this->description,
            'destination_account_id' => $this->destinationAccountId,
            'payment_method' => $this->paymentMethod,
            'status' => $this->status,
            'transaction_date' => $this->transactionDate,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
