<?php

namespace App\ValueObjects;

use Illuminate\Support\Number;

class MoneyValue
{
    /**
     * @param int $amount the given amount in cents
     */
    public function __construct(private int $amount)
    {}

    public function getRawAmount(): int
    {
        return $this->amount;
    }

    public static function from(int $amount): self
    {
        return new self($amount);
    }

    public static function fromBRL(string $value): self
    {
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return new self((int) round(floatval($value) * 100));
    }

    public function toBRL(): string
    {
        return Number::format($this->amount / 100, precision: 2, locale: 'pt_BR');
    }
}
