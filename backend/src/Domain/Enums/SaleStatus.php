<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Enums;

enum SaleStatus: string
{
    case Pending   = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Refunded  = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'Pendente',
            self::Completed => 'Concluída',
            self::Cancelled => 'Cancelada',
            self::Refunded  => 'Reembolsada',
        };
    }
}
