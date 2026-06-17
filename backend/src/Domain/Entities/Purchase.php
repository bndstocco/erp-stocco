<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Entities;

class Purchase
{
    private ?int $id;
    private string $purchaseOrder;
    private ?int $supplierId;
    private int $userId;
    private float $subtotal;
    private float $discount;
    private float $total;
    private string $status;
    private ?string $notes;
    private ?string $purchaseDate;
    private ?string $createdAt;
    private ?string $updatedAt;
    private array $items;

    public function __construct(
        ?int $id = null,
        string $purchaseOrder = '',
        ?int $supplierId = null,
        int $userId = 0,
        float $subtotal = 0.0,
        float $discount = 0.0,
        float $total = 0.0,
        string $status = 'pending',
        ?string $notes = null,
        ?string $purchaseDate = null,
        ?string $createdAt = null,
        ?string $updatedAt = null,
        array $items = []
    ) {
        $this->id = $id;
        $this->purchaseOrder = $purchaseOrder;
        $this->supplierId = $supplierId;
        $this->userId = $userId;
        $this->subtotal = $subtotal;
        $this->discount = $discount;
        $this->total = $total;
        $this->status = $status;
        $this->notes = $notes;
        $this->purchaseDate = $purchaseDate;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->items = $items;
    }

    public function getId(): ?int { return $this->id; }
    public function getPurchaseOrder(): string { return $this->purchaseOrder; }
    public function getSupplierId(): ?int { return $this->supplierId; }
    public function getUserId(): int { return $this->userId; }
    public function getSubtotal(): float { return $this->subtotal; }
    public function getDiscount(): float { return $this->discount; }
    public function getTotal(): float { return $this->total; }
    public function getStatus(): string { return $this->status; }
    public function getNotes(): ?string { return $this->notes; }
    public function getPurchaseDate(): ?string { return $this->purchaseDate; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function getItems(): array { return $this->items; }

    public function setId(int $id): void { $this->id = $id; }
    public function setPurchaseOrder(string $purchaseOrder): void { $this->purchaseOrder = $purchaseOrder; }
    public function setSupplierId(?int $supplierId): void { $this->supplierId = $supplierId; }
    public function setUserId(int $userId): void { $this->userId = $userId; }
    public function setSubtotal(float $subtotal): void { $this->subtotal = $subtotal; }
    public function setDiscount(float $discount): void { $this->discount = $discount; }
    public function setTotal(float $total): void { $this->total = $total; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setNotes(?string $notes): void { $this->notes = $notes; }
    public function setPurchaseDate(?string $purchaseDate): void { $this->purchaseDate = $purchaseDate; }
    public function setItems(array $items): void { $this->items = $items; }

    public function addItem(PurchaseItem $item): void
    {
        $this->items[] = $item;
        $this->recalculate();
    }

    public function recalculate(): void
    {
        $this->subtotal = array_sum(array_map(fn(PurchaseItem $i) => $i->getSubtotal(), $this->items));
        $this->total = $this->subtotal - $this->discount;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'purchase_order' => $this->purchaseOrder,
            'supplier_id' => $this->supplierId,
            'user_id' => $this->userId,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'total' => $this->total,
            'status' => $this->status,
            'notes' => $this->notes,
            'purchase_date' => $this->purchaseDate,
            'items' => array_map(fn(PurchaseItem $i) => $i->toArray(), $this->items),
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
