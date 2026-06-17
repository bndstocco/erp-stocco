<?php

declare(strict_types=1);

namespace ErpStocco\Domain\ValueObjects;

final class Address
{
    public function __construct(
        private string $street,
        private string $number,
        private string $complement = '',
        private string $neighborhood = '',
        private string $city = '',
        private string $state = '',
        private string $zipcode = ''
    ) {}

    public function street(): string { return $this->street; }
    public function number(): string { return $this->number; }
    public function complement(): string { return $this->complement; }
    public function neighborhood(): string { return $this->neighborhood; }
    public function city(): string { return $this->city; }
    public function state(): string { return $this->state; }
    public function zipcode(): string { return $this->zipcode; }

    public function full(): string
    {
        $parts = [
            $this->street,
            $this->number ? ", {$this->number}" : '',
            $this->complement ? " - {$this->complement}" : '',
            $this->neighborhood ? ", {$this->neighborhood}" : '',
            $this->city ? " - {$this->city}" : '',
            $this->state ? "/{$this->state}" : '',
            $this->zipcode ? " - {$this->zipcode}" : '',
        ];

        return implode('', $parts);
    }

    public function __toString(): string
    {
        return $this->full();
    }

    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'number' => $this->number,
            'complement' => $this->complement,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'zipcode' => $this->zipcode,
        ];
    }
}
