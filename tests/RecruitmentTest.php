<?php

use IFX\Domain\Entity\ExchangeRate;
use IFX\Domain\ValueObject\CurrencyValueObject;
use IFX\Infrastructure\Repository\ExchangeRateRepository;
use IFX\Infrastructure\Service\ExchangeOfficeService;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class RecruitmentTest extends TestCase
{
    public const float RATE_EUR_GBP = 1.5678;
    public const float RATE_GBP_EUR = 1.5432;
    public const float FEE = 0.01;

    /**
     * case1: Klient sprzedaje 100 EUR za GBP
     */
    public function testUserSells100Eur(): void
    {
        // 100 EUR * 0.01 = 1 EUR fee
        // EUR -> GBP  =  1.5678
        // 99 EUR * 1.5678 = 155 GBP
        $eur100 = Money::EUR(10000);
        $eur100 = $eur100->multiply(1 - self::FEE);
        $expectedAmount = $eur100->multiply(self::RATE_EUR_GBP)->getAmount();
        $expectedGbp = (new Money($expectedAmount, new Currency('GBP')))->divide(100)->getAmount();

        $exchangeOfficeService = $this->getExchangeOfficeService();
        $actual = $exchangeOfficeService->buy(Money::EUR(10000), new Currency("GBP"));

        $this->assertEquals($expectedGbp, $actual->divide(100)->getAmount());
    }

    /**
     * case2: Klient kupuje 100 GBP za EUR
     */
    public function testUserBuys100Eur(): void
    {
        // x EUR -> 100 GBP
        // EUR -> GBP  =  1.5678
        // x EUR * 1.5678 = 100 GBB
        // 63.7836 * 0.99 = 63.1
        // 63 EUR
        $gbp100 = Money::GBP(10000);
        $converted = $gbp100->divide(self::RATE_EUR_GBP)->getAmount();
        $expectedEur = (new Money($converted, new Currency("EUR")))->multiply(1 - self::FEE)->divide(100)->getAmount();

        $exchangeOfficeService = $this->getExchangeOfficeService();
        $actual = $exchangeOfficeService->sell(Money::GBP(10000), new Currency("EUR"));
        $this->assertEquals($expectedEur, $actual->divide(100)->getAmount());
    }

    /**
     * case3: Klient sprzedaje 100 GBP za EUR
     */
    public function testUserSells100Gbp(): void
    {
        // 100 GBP * 0.01 = 1 GBP fee
        // GBP -> EUR  =  1.5432
        // 99 GBP * 1.5432 = 153 GBP
        $gbp100 = Money::GBP(10000);
        $gbp100 = $gbp100->multiply(1 - self::FEE);
        $expectedAmount = $gbp100->multiply(self::RATE_GBP_EUR)->getAmount();
        $expectedEur = (new Money($expectedAmount, new Currency('EUR')))->divide(100)->getAmount();

        $exchangeOfficeService = $this->getExchangeOfficeService();
        $actual = $exchangeOfficeService->buy(Money::GBP(10000), new Currency("EUR"));

        $this->assertEquals($expectedEur, $actual->divide(100)->getAmount());

    }

    /**
     * case4: Klient kupuje 100 EUR za GBP
     */
    public function testUserBuys100Gbp(): void
    {
        // x GBP -> 100 GBB
        // GBP -> EUR  =  1.5432
        // x GBP * 1.5432 = 100 EUR
        // 64.8 * 0.99 = 64.152
        // 64 GBP
        $eur100 = Money::EUR(10000);
        $converted = $eur100->divide(self::RATE_GBP_EUR)->getAmount();
        $expectedGbp = (new Money($converted, new Currency("GBP")))->multiply(1 - self::FEE)->divide(100)->getAmount();

        $exchangeOfficeService = $this->getExchangeOfficeService();
        $actual = $exchangeOfficeService->sell(Money::EUR(10000), new Currency("GBP"));
        $this->assertEquals($expectedGbp, $actual->divide(100)->getAmount());
    }

    private function getExchangeOfficeService(): ExchangeOfficeService
    {
        $eur = CurrencyValueObject::Create("EUR", "Euro", 2, 978);
        $gbp = CurrencyValueObject::Create("GBP", "Pound Sterling", 2, 826);
        $eurToGbp = new ExchangeRate($eur, $gbp, self::RATE_EUR_GBP);
        $gbpToEur = new ExchangeRate($gbp, $eur, self::RATE_GBP_EUR);

        $currencyRepository = new ExchangeRateRepository();
        $currencyRepository->add($eurToGbp);
        $currencyRepository->add($gbpToEur);

        return new ExchangeOfficeService($currencyRepository->getAll(), self::FEE);
    }
}