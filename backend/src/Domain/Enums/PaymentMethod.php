<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Enums;

enum PaymentMethod: string
{
    case Cash       = 'cash';
    case CreditCard = 'credit_card';
    case DebitCard  = 'debit_card';
    case Pix        = 'pix';
    case Transfer   = 'transfer';
    case Boleto     = 'boleto';
    case Other      = 'other';

    public function label(): string
    {
        return match($this) {
            self::Cash       => 'Dinheiro',
            self::CreditCard => 'Cartão de Crédito',
            self::DebitCard  => 'Cartão de Débito',
            self::Pix        => 'PIX',
            self::Transfer   => 'Transferência',
            self::Boleto     => 'Boleto',
            self::Other      => 'Outro',
        };
    }
}
