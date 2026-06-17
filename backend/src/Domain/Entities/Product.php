<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Entities;

class Product
{
    private ?int $id;
    private string $name;
    private ?string $description;
    private string $sku;
    private ?string $barcode;
    private ?int $categoryId;
    private float $unitPrice;
    private float $costPrice;
    private int $stockQuantity;
    private int $minStock;
    private ?int $maxStock;
    private string $unit;
    private ?float $weight;
    private string $status;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id = null,
        string $name = '',
        ?string $description = null,
        string $sku = '',
        ?string $barcode = null,
        ?int $categoryId = null,
        float $unitPrice = 0.0,
        float $costPrice = 0.0,
        int $stockQuantity = 0,
        int $minStock = 0,
        ?int $maxStock = null,
        string $unit = 'un',
        ?float $weight = null,
        string $status = 'active',
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->sku = $sku;
        $this->barcode = $barcode;
        $this->categoryId = $categoryId;
        $this->unitPrice = $unitPrice;
        $this->costPrice = $costPrice;
        $this->stockQuantity = $stockQuantity;
        $this->minStock = $minStock;
        $this->maxStock = $maxStock;
        $this->unit = $unit;
        $this->weight = $weight;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDescription(): ?string { return $this->description; }
    public function getSku(): string { return $this->sku; }
    public function getBarcode(): ?string { return $this->barcode; }
    public function getCategoryId(): ?int { return $this->categoryId; }
    public function getUnitPrice(): float { return $this->unitPrice; }
    public function getCostPrice(): float { return $this->costPrice; }
    public function getStockQuantity(): int { return $this->stockQuantity; }
    public function getMinStock(): int { return $this->minStock; }
    public function getMaxStock(): ?int { return $this->maxStock; }
    public function getUnit(): string { return $this->unit; }
    public function getWeight(): ?float { return $this->weight; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = $name; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setSku(string $sku): void { $this->sku = $sku; }
    public function setBarcode(?string $barcode): void { $this->barcode = $barcode; }
    public function setCategoryId(?int $categoryId): void { $this->categoryId = $categoryId; }
    public function setUnitPrice(float $unitPrice): void { $this->unitPrice = $unitPrice; }
    public function setCostPrice(float $costPrice): void { $this->costPrice = $costPrice; }
    public function setStockQuantity(int $stockQuantity): void { $this->stockQuantity = $stockQuantity; }
    public function setMinStock(int $minStock): void { $this->minStock = $minStock; }
    public function setMaxStock(?int $maxStock): void { $this->maxStock = $maxStock; }
    public function setUnit(string $unit): void { $this->unit = $unit; }
    public function setWeight(?float $weight): void { $this->weight = $weight; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function isActive(): bool { return $this->status === 'active'; }
    public function isLowStock(): bool { return $this->stockQuantity <= $this->minStock; }
    public function profitMargin(): float
    {
        if ($this->costPrice <= 0) return 0;
        return round((($this->unitPrice - $this->costPrice) / $this->costPrice) * 100, 2);
    }

    public function addStock(int $quantity): void { $this->stockQuantity += $quantity; }
    public function removeStock(int $quantity): void { $this->stockQuantity -= $quantity; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'category_id' => $this->categoryId,
            'unit_price' => $this->unitPrice,
            'cost_price' => $this->costPrice,
            'stock_quantity' => $this->stockQuantity,
            'min_stock' => $this->minStock,
            'max_stock' => $this->maxStock,
            'unit' => $this->unit,
            'weight' => $this->weight,
            'status' => $this->status,
            'is_low_stock' => $this->isLowStock(),
            'profit_margin' => $this->profitMargin(),
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
