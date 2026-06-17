<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Enums;

enum EmployeeStatus: string
{
    case Active      = 'active';
    case Inactive    = 'inactive';
    case Terminated  = 'terminated';

    public function label(): string
    {
        return match($this) {
            self::Active     => 'Ativo',
            self::Inactive   => 'Inativo',
            self::Terminated => 'Desligado',
        };
    }
}
