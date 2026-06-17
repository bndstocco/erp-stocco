<?php

declare(strict_types=1);

namespace ErpStocco\Domain\ValueObjects;

use InvalidArgumentException;

final class Document
{
    private string $value;

    public function __construct(string $value)
    {
        $clean = preg_replace('/[^0-9]/', '', $value);

        if (strlen($clean) !== 11 && strlen($clean) !== 14) {
            throw new InvalidArgumentException('Documento inválido. Deve ter 11 (CPF) ou 14 (CNPJ) dígitos');
        }

        $this->value = $clean;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function type(): string
    {
        return strlen($this->value) === 11 ? 'CPF' : 'CNPJ';
    }

    public function formatted(): string
    {
        if (strlen($this->value) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->value);
        }

        return preg_replace(
            '/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/',
            '$1.$2.$3/$4-$5',
            $this->value
        );
    }

    public function __toString(): string
    {
        return $this->formatted();
    }
}
