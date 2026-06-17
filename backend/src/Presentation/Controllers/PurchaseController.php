<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Controllers;

use ErpStocco\Infrastructure\Repositories\MySQLPurchaseRepository;

class PurchaseController
{
    private MySQLPurchaseRepository $repository;

    public function __construct()
    {
        $this->repository = new MySQLPurchaseRepository();
    }

    public function index(): void
    {
        $filters = array_merge($_GET, ['per_page' => $_GET['per_page'] ?? 15]);
        $result = $this->repository->findAll($filters);
        echo json_encode(['error' => false, 'data' => $result]);
    }

    public function show(int $id): void
    {
        $purchase = $this->repository->findById($id);
        if (!$purchase) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Compra não encontrada']);
            return;
        }
        echo json_encode(['error' => false, 'data' => $purchase->toArray()]);
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['items'])) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Itens da compra são obrigatórios']);
            return;
        }

        $items = [];
        $subtotal = 0;

        foreach ($data['items'] as $itemData) {
            $item = new \ErpStocco\Domain\Entities\PurchaseItem(
                productId: isset($itemData['product_id']) ? (int) $itemData['product_id'] : null,
                productName: $itemData['product_name'],
                quantity: (int) $itemData['quantity'],
                unitPrice: (float) $itemData['unit_price'],
                subtotal: (int) $itemData['quantity'] * (float) $itemData['unit_price'],
            );
            $item->calculateSubtotal();
            $items[] = $item;
            $subtotal += $item->getSubtotal();
        }

        $discount = (float) ($data['discount'] ?? 0);

        $purchase = new \ErpStocco\Domain\Entities\Purchase(
            purchaseOrder: 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
            supplierId: isset($data['supplier_id']) ? (int) $data['supplier_id'] : null,
            userId: 1,
            subtotal: $subtotal,
            discount: $discount,
            total: $subtotal - $discount,
            status: 'pending',
            notes: $data['notes'] ?? null,
            purchaseDate: date('Y-m-d H:i:s'),
        );

        foreach ($items as $item) {
            $purchase->addItem($item);
        }

        $this->repository->save($purchase);
        http_response_code(201);
        echo json_encode(['error' => false, 'data' => $purchase->toArray()]);
    }

    public function update(int $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $purchase = $this->repository->findById($id);

        if (!$purchase) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Compra não encontrada']);
            return;
        }

        if (isset($data['status'])) $purchase->setStatus($data['status']);
        if (isset($data['notes'])) $purchase->setNotes($data['notes']);

        $this->repository->update($purchase);
        echo json_encode(['error' => false, 'data' => $purchase->toArray()]);
    }

    public function destroy(int $id): void
    {
        $purchase = $this->repository->findById($id);
        if (!$purchase) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Compra não encontrada']);
            return;
        }

        $this->repository->delete($id);
        echo json_encode(['error' => false, 'message' => 'Compra cancelada com sucesso']);
    }
}
