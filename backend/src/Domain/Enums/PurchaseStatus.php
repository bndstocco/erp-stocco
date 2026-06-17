<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Enums;

enum PurchaseStatus: string
{
    case Pending   = 'pending';
    case Approved  = 'approved';
    case Received  = 'received';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'Pendente',
            self::Approved  => 'Aprovada',
            self::Received  => 'Recebida',
            self::Cancelled => 'Cancelada',
        };
    }
}
