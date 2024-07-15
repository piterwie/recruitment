<?php

namespace Tests;

use IFX\Domain\Entity\ExchangeRate;
use IFX\Domain\Entity\User;
use IFX\Domain\ValueObject\CurrencyValueObject;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    public function testComparingEntities(): void
    {
        $eur = CurrencyValueObject::Create("EUR", "Euro", 2, 978);
        $gbp = CurrencyValueObject::Create("GBP", "Pound Sterling", 2, 826);
        $exchangeRate = new ExchangeRate($eur, $gbp, 3.14);
        $exchangeRate2 = new ExchangeRate($gbp, $eur, 3.14);

        $this->assertTrue($exchangeRate->isEqual($exchangeRate));
        $this->assertFalse($exchangeRate->isEqual($exchangeRate2));
    }

    public function testGeneratingUUID(): void
    {
        $user = new User();
        $this->assertNotEmpty($user->getId());
    }
}