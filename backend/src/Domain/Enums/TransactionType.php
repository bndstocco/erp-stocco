<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Enums;

enum TransactionType: string
{
    case Income  = 'income';
    case Expense = 'expense';
    case Transfer = 'transfer';

    public function label(): string
    {
        return match($this) {
            self::Income    => 'Receita',
            self::Expense   => 'Despesa',
            self::Transfer  => 'Transferência',
        };
    }
}
