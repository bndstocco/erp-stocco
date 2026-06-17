<?php

declare(strict_types=1);

namespace ErpStocco\Domain\ValueObjects;

use InvalidArgumentException;

final class Money
{
    private float $amount;
    private string $currency;

    public function __construct(float $amount, string $currency = 'BRL')
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Valor monetário não pode ser negativo');
        }

        $this->amount = round($amount, 2);
        $this->currency = strtoupper($currency);
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function add(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Moedas diferentes não podem ser somadas');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Moedas diferentes não podem ser subtraídas');
        }

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiply(float $factor): self
    {
        return new self($this->amount * $factor, $this->currency);
    }

    public function format(): string
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
