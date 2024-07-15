<?php

namespace Tests;

use IFX\Domain\Entity\ExchangeRate;
use IFX\Domain\Exception\ExchangeRateNotFound;
use IFX\Domain\ValueObject\CurrencyValueObject;
use IFX\Infrastructure\Repository\ExchangeRateRepository;
use PHPUnit\Framework\TestCase;

final class RepositoryTest extends TestCase
{
    public function testGetting(): void
    {
        $eur = CurrencyValueObject::Create("EUR", "Euro", 2, 978);
        $gbp = CurrencyValueObject::Create("GBP", "Pound Sterling", 2, 826);
        $eurToGbp = new ExchangeRate($eur, $gbp, 3.14);

        $exchangeRateRepository = new ExchangeRateRepository();
        $exchangeRateRepository->add($eurToGbp);
        $fromRepo = $exchangeRateRepository->get($eurToGbp->getId());
        $this->assertEquals($eurToGbp, $fromRepo);
    }

    public function testGettingNotExisted()
    {
        $eur = CurrencyValueObject::Create("EUR", "Euro", 2, 978);
        $gbp = CurrencyValueObject::Create("GBP", "Pound Sterling", 2, 826);
        $eurToGbp = new ExchangeRate($eur, $gbp, 3.14);

        $currencyRepository = new ExchangeRateRepository();
        $this->expectException(ExchangeRateNotFound::class);
        $currencyRepository->get($eurToGbp->getId());
    }
}