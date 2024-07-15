<?php

namespace Tests;

use IFX\Domain\ValueObject\CurrencyValueObject;
use IFX\Domain\ValueObject\ExchangeRateValueObject;
use PHPUnit\Framework\TestCase;

class ValueObjectTest extends TestCase
{
    public function testComparingValueObjects(): void
    {
        $eur = CurrencyValueObject::Create("EUR", "Euro", 2, 978);
        $gbp = CurrencyValueObject::Create("GBP", "Pound Sterling", 2, 826);

        $this->assertTrue($eur->isEqual($eur));
        $this->assertFalse($eur->isEqual($gbp));
    }
}