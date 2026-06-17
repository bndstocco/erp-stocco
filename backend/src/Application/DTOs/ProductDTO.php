<?php

declare(strict_types=1);

namespace ErpStocco\Application\DTOs;

class ProductDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $sku,
        public readonly ?string $barcode,
        public readonly ?int $categoryId,
        public readonly ?string $categoryName,
        public readonly float $unitPrice,
        public readonly float $costPrice,
        public readonly int $stockQuantity,
        public readonly int $minStock,
        public readonly ?int $maxStock,
        public readonly string $unit,
        public readonly ?float $weight,
        public readonly string $status,
        public readonly bool $isLowStock,
        public readonly float $profitMargin,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? '',
            description: $data['description'] ?? null,
            sku: $data['sku'] ?? '',
            barcode: $data['barcode'] ?? null,
            categoryId: $data['category_id'] ?? null,
            categoryName: $data['category_name'] ?? null,
            unitPrice: (float) ($data['unit_price'] ?? 0),
            costPrice: (float) ($data['cost_price'] ?? 0),
            stockQuantity: (int) ($data['stock_quantity'] ?? 0),
            minStock: (int) ($data['min_stock'] ?? 0),
            maxStock: $data['max_stock'] ?? null,
            unit: $data['unit'] ?? 'un',
            weight: $data['weight'] ? (float) $data['weight'] : null,
            status: $data['status'] ?? 'active',
            isLowStock: (bool) ($data['is_low_stock'] ?? false),
            profitMargin: (float) ($data['profit_margin'] ?? 0),
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'category_id' => $this->categoryId,
            'category_name' => $this->categoryName,
            'unit_price' => $this->unitPrice,
            'cost_price' => $this->costPrice,
            'stock_quantity' => $this->stockQuantity,
            'min_stock' => $this->minStock,
            'max_stock' => $this->maxStock,
            'unit' => $this->unit,
            'weight' => $this->weight,
            'status' => $this->status,
            'is_low_stock' => $this->isLowStock,
            'profit_margin' => $this->profitMargin,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
