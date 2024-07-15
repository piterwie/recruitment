<?php

namespace Tests;

use IFX\Domain\Entity\ExchangeRate;
use IFX\Domain\ValueObject\CurrencyValueObject;
use PHPUnit\Framework\TestCase;

class ExchangeRateTest extends TestCase
{
    public function testCreatingExchangeRate(): void
    {
        $eur = CurrencyValueObject::Create("EUR", "Euro", 2, 978);
        $gbp = CurrencyValueObject::Create("GBP", "Pound Sterling", 2, 826);
        $exchangeRate = new ExchangeRate($eur, $gbp, 3.14);

        $this->assertEquals("EUR_GBP", $exchangeRate->getId());
        $this->assertEquals($eur, $exchangeRate->getFrom());
        $this->assertEquals($gbp, $exchangeRate->getTo());
        $this->assertEquals(3.14, $exchangeRate->getRate());
    }
}