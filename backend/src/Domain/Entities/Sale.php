<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Entities;

class Sale
{
    private ?int $id;
    private string $invoiceNumber;
    private ?int $customerId;
    private ?string $customerName;
    private int $userId;
    private ?string $userName;
    private float $subtotal;
    private float $discount;
    private float $total;
    private string $paymentMethod;
    private int $installmentCount;
    private string $status;
    private ?string $notes;
    private ?string $saleDate;
    private ?string $createdAt;
    private ?string $updatedAt;
    private array $items;

    public function __construct(
        ?int $id = null,
        string $invoiceNumber = '',
        ?int $customerId = null,
        ?string $customerName = null,
        int $userId = 0,
        ?string $userName = null,
        float $subtotal = 0.0,
        float $discount = 0.0,
        float $total = 0.0,
        string $paymentMethod = 'cash',
        int $installmentCount = 1,
        string $status = 'pending',
        ?string $notes = null,
        ?string $saleDate = null,
        ?string $createdAt = null,
        ?string $updatedAt = null,
        array $items = []
    ) {
        $this->id = $id;
        $this->invoiceNumber = $invoiceNumber;
        $this->customerId = $customerId;
        $this->customerName = $customerName;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->subtotal = $subtotal;
        $this->discount = $discount;
        $this->total = $total;
        $this->paymentMethod = $paymentMethod;
        $this->installmentCount = $installmentCount;
        $this->status = $status;
        $this->notes = $notes;
        $this->saleDate = $saleDate;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->items = $items;
    }

    public function getId(): ?int { return $this->id; }
    public function getInvoiceNumber(): string { return $this->invoiceNumber; }
    public function getCustomerId(): ?int { return $this->customerId; }
    public function getCustomerName(): ?string { return $this->customerName; }
    public function getUserId(): int { return $this->userId; }
    public function getUserName(): ?string { return $this->userName; }
    public function getSubtotal(): float { return $this->subtotal; }
    public function getDiscount(): float { return $this->discount; }
    public function getTotal(): float { return $this->total; }
    public function getPaymentMethod(): string { return $this->paymentMethod; }
    public function getInstallmentCount(): int { return $this->installmentCount; }
    public function getStatus(): string { return $this->status; }
    public function getNotes(): ?string { return $this->notes; }
    public function getSaleDate(): ?string { return $this->saleDate; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function getItems(): array { return $this->items; }

    public function setId(int $id): void { $this->id = $id; }
    public function setInvoiceNumber(string $invoiceNumber): void { $this->invoiceNumber = $invoiceNumber; }
    public function setCustomerId(?int $customerId): void { $this->customerId = $customerId; }
    public function setUserId(int $userId): void { $this->userId = $userId; }
    public function setSubtotal(float $subtotal): void { $this->subtotal = $subtotal; }
    public function setDiscount(float $discount): void { $this->discount = $discount; }
    public function setTotal(float $total): void { $this->total = $total; }
    public function setPaymentMethod(string $paymentMethod): void { $this->paymentMethod = $paymentMethod; }
    public function setInstallmentCount(int $installmentCount): void { $this->installmentCount = $installmentCount; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setNotes(?string $notes): void { $this->notes = $notes; }
    public function setSaleDate(?string $saleDate): void { $this->saleDate = $saleDate; }
    public function setItems(array $items): void { $this->items = $items; }

    public function addItem(SaleItem $item): void
    {
        $this->items[] = $item;
        $this->recalculate();
    }

    public function recalculate(): void
    {
        $this->subtotal = array_sum(array_map(fn(SaleItem $i) => $i->getSubtotal(), $this->items));
        $this->total = $this->subtotal - $this->discount;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoiceNumber,
            'customer_id' => $this->customerId,
            'customer_name' => $this->customerName,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'total' => $this->total,
            'payment_method' => $this->paymentMethod,
            'installment_count' => $this->installmentCount,
            'status' => $this->status,
            'notes' => $this->notes,
            'sale_date' => $this->saleDate,
            'items' => array_map(fn(SaleItem $i) => $i->toArray(), $this->items),
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
