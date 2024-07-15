<?php

namespace IFX\Domain\Entity;

use IFX\Domain\ValueObject\CurrencyValueObject;

final class ExchangeRate extends Entity
{
    private CurrencyValueObject $from;
    private CurrencyValueObject $to;
    private float $rate;

    public function __construct(CurrencyValueObject $from, CurrencyValueObject $to, float $rate)
    {
        $this->id = $from->getAlphabeticCode() . '_' . $to->getAlphabeticCode();
        $this->from = $from;
        $this->to = $to;
        $this->rate = $rate;
    }

    public function getFrom(): CurrencyValueObject
    {
        return $this->from;
    }

    public function getTo(): CurrencyValueObject
    {
        return $this->to;
    }

    public function getRate(): float
    {
        return $this->rate;
    }
}