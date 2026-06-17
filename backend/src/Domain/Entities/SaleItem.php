<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Entities;

class SaleItem
{
    private ?int $id;
    private int $saleId;
    private ?int $productId;
    private string $productName;
    private int $quantity;
    private float $unitPrice;
    private float $subtotal;
    private ?string $createdAt;

    public function __construct(
        ?int $id = null,
        int $saleId = 0,
        ?int $productId = null,
        string $productName = '',
        int $quantity = 1,
        float $unitPrice = 0.0,
        float $subtotal = 0.0,
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->saleId = $saleId;
        $this->productId = $productId;
        $this->productName = $productName;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->subtotal = $subtotal;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getSaleId(): int { return $this->saleId; }
    public function getProductId(): ?int { return $this->productId; }
    public function getProductName(): string { return $this->productName; }
    public function getQuantity(): int { return $this->quantity; }
    public function getUnitPrice(): float { return $this->unitPrice; }
    public function getSubtotal(): float { return $this->subtotal; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    public function setId(?int $id): void { $this->id = $id; }
    public function setSaleId(int $saleId): void { $this->saleId = $saleId; }
    public function setProductId(?int $productId): void { $this->productId = $productId; }
    public function setProductName(string $productName): void { $this->productName = $productName; }
    public function setQuantity(int $quantity): void { $this->quantity = $quantity; }
    public function setUnitPrice(float $unitPrice): void { $this->unitPrice = $unitPrice; }
    public function setSubtotal(float $subtotal): void { $this->subtotal = $subtotal; }

    public function calculateSubtotal(): void
    {
        $this->subtotal = $this->quantity * $this->unitPrice;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sale_id' => $this->saleId,
            'product_id' => $this->productId,
            'product_name' => $this->productName,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'subtotal' => $this->subtotal,
            'created_at' => $this->createdAt,
        ];
    }
}
