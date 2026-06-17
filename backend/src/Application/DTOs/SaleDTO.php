<?php

declare(strict_types=1);

namespace ErpStocco\Application\DTOs;

class SaleDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $invoiceNumber,
        public readonly ?int $customerId,
        public readonly ?string $customerName,
        public readonly int $userId,
        public readonly ?string $userName,
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $total,
        public readonly string $paymentMethod,
        public readonly int $installmentCount,
        public readonly string $status,
        public readonly ?string $notes,
        public readonly ?string $saleDate,
        public readonly array $items,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            invoiceNumber: $data['invoice_number'] ?? '',
            customerId: $data['customer_id'] ?? null,
            customerName: $data['customer_name'] ?? null,
            userId: $data['user_id'] ?? 0,
            userName: $data['user_name'] ?? null,
            subtotal: (float) ($data['subtotal'] ?? 0),
            discount: (float) ($data['discount'] ?? 0),
            total: (float) ($data['total'] ?? 0),
            paymentMethod: $data['payment_method'] ?? 'cash',
            installmentCount: (int) ($data['installment_count'] ?? 1),
            status: $data['status'] ?? 'pending',
            notes: $data['notes'] ?? null,
            saleDate: $data['sale_date'] ?? null,
            items: $data['items'] ?? [],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
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
            'items' => $this->items,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
