<?php

namespace Tests;

use IFX\Domain\Entity\ExchangeRate;
use IFX\Domain\ValueObject\CurrencyValueObject;
use IFX\Infrastructure\Repository\ExchangeRateRepository;
use IFX\Infrastructure\Service\Exceptions\InvalidFeeException;
use IFX\Infrastructure\Service\ExchangeOfficeService;
use Money\Currency;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class ExchangeOfficeServiceTest extends TestCase
{
    public function testCreatingExchangeOfficeServiceWithTooLowFee(): void
    {
        $tooLowFee = -3.14;
        $this->expectException(InvalidFeeException::class);
        new ExchangeOfficeService([], $tooLowFee);
    }

    public function testCreatingExchangeOfficeServiceWithTooHighFee(): void
    {
        $tooHighFee = 3.14;
        $this->expectException(InvalidFeeException::class);
        new ExchangeOfficeService([], $tooHighFee);
    }

    public function testBuyWithNoFeeExchangeOfficeService(): void
    {
        $eur = CurrencyValueObject::Create("EUR", "Euro", 2, 978);
        $gbp = CurrencyValueObject::Create("GBP", "Pound Sterling", 2, 826);
        $eurToGbp = new ExchangeRate($eur, $gbp, 1.5678);
        $gbpToEur = new ExchangeRate($gbp, $eur, 1.5432);

        $exchangeRateRepository = new ExchangeRateRepository();
        $exchangeRateRepository->add($eurToGbp);
        $exchangeRateRepository->add($gbpToEur);

        $noFee = 0.0;
        $service = new ExchangeOfficeService($exchangeRateRepository->getAll(), $noFee);

        //user comes to exchange office wants to sell $amountEur of EUR and gets GBP
        $amountEur = Money::EUR(100);
        $expectedGBP = $amountEur->multiply($eurToGbp->getRate());

        $gbpResult = $service->buy($amountEur, new Currency("GBP"));
        $this->assertEquals($expectedGBP->getAmount(), $gbpResult->getAmount());

        //user comes to exchange office wants to sell $amountGbp of GBP and gets EUR
        $amountGbp = Money::GBP(200);
        $expectedEur = $amountGbp->multiply($gbpToEur->getRate());

        $eurResult = $service->buy($amountGbp, new Currency("EUR"));
        $this->assertEquals($expectedEur->getAmount(), $eurResult->getAmount());
    }

    public function testSellWithNoFeeExchangeOfficeService(): void
    {
        $eur = CurrencyValueObject::Create("EUR", "Euro", 2, 978);
        $gbp = CurrencyValueObject::Create("GBP", "Pound Sterling", 2, 826);
        $eurToGbp = new ExchangeRate($eur, $gbp, 1.5678);
        $gbpToEur = new ExchangeRate($gbp, $eur, 1.5432);

        $exchangeRateRepository = new ExchangeRateRepository();
        $exchangeRateRepository->add($eurToGbp);
        $exchangeRateRepository->add($gbpToEur);

        $noFee = 0.0;
        $service = new ExchangeOfficeService($exchangeRateRepository->getAll(), $noFee);

        //user comes to exchange office wants to buy $amountEur of EUR and pay with GBP
        $amountEur = Money::EUR(300);
        $expectedGBP = $amountEur->divide($gbpToEur->getRate());

        $gbpResult = $service->sell($amountEur, new Currency("GBP"));
        $this->assertEquals($expectedGBP->getAmount(), $gbpResult->getAmount());

        //user comes to exchange office wants to sell $amountGbp of GBP and gets Eur
        $amountGbp = Money::GBP(400);
        $expectedEur = $amountGbp->multiply($gbpToEur->getRate());

        $eurResult = $service->buy($amountGbp, new Currency("EUR"));
        $this->assertEquals($expectedEur->getAmount(), $eurResult->getAmount());
    }

    public function testBuyWithFeeExchangeOfficeService(): void
    {
        $eur = CurrencyValueObject::Create("EUR", "Euro", 2, 978);
        $gbp = CurrencyValueObject::Create("GBP", "Pound Sterling", 2, 826);
        $eurToGbp = new ExchangeRate($eur, $gbp, 1.5678);
        $gbpToEur = new ExchangeRate($gbp, $eur, 1.5432);

        $exchangeRateRepository = new ExchangeRateRepository();
        $exchangeRateRepository->add($eurToGbp);
        $exchangeRateRepository->add($gbpToEur);

        $fee = 0.02;
        $service = new ExchangeOfficeService($exchangeRateRepository->getAll(), $fee);

        //user comes to exchange office wants to sell $amountEur of EUR and gets GBP
        $amountEur = Money::EUR(100);
        $expectedGBP = $amountEur->multiply(1-$fee);
        $expectedGBP = $expectedGBP->multiply($eurToGbp->getRate());

        $gbpResult = $service->buy($amountEur, new Currency("GBP"));
        $this->assertEquals($expectedGBP->getAmount(), $gbpResult->getAmount());

        //user comes to exchange office wants to sell $amountGbp of GBP and gets EUR
        $amountGbp = Money::GBP(500);
        $expectedEur = $amountGbp->multiply(1-$fee);
        $expectedEur = $expectedEur->multiply($gbpToEur->getRate());

        $eurResult = $service->buy($amountGbp, new Currency("EUR"));
        $this->assertEquals($expectedEur->getAmount(), $eurResult->getAmount());
    }

    public function testSellWithFeeExchangeOfficeService(): void
    {
        $eur = CurrencyValueObject::Create("EUR", "Euro", 2, 978);
        $gbp = CurrencyValueObject::Create("GBP", "Pound Sterling", 2, 826);
        $eurToGbp = new ExchangeRate($eur, $gbp, 1.5678);
        $gbpToEur = new ExchangeRate($gbp, $eur, 1.5432);

        $exchangeRateRepository = new ExchangeRateRepository();
        $exchangeRateRepository->add($eurToGbp);
        $exchangeRateRepository->add($gbpToEur);

        $fee = 0.03;
        $service = new ExchangeOfficeService($exchangeRateRepository->getAll(), $fee);

        //user comes to exchange office wants to buy $amountEur of EUR and pay with GBP
        $amountEur = Money::EUR(600);
        $expectedGbp = Money::GBP($amountEur->divide($gbpToEur->getRate())->getAmount());
        $expectedGbp = $expectedGbp->multiply(1-$fee);

        $actualGbp = $service->sell($amountEur, new Currency("GBP"));
        $this->assertEquals($expectedGbp->getAmount(), $actualGbp->getAmount());

        //user comes to exchange office wants to sell $amountGbp of GBP and gets Eur
        $amountGbp = Money::GBP(700);
        $expectedEur = Money::EUR($amountGbp->divide($eurToGbp->getRate())->getAmount());
        $expectedEur = $expectedEur->multiply(1-$fee);

        $actualEur = $service->sell($amountGbp, new Currency("EUR"));
        $this->assertEquals($expectedEur->getAmount(), $actualEur->getAmount());
    }

    public function testBuyNotDefinedCurrency(): void
    {
        $fee = 0.04;
        $service = new ExchangeOfficeService([], $fee);

        $amount = new Money(300, new Currency("EUR"));

        $this->expectException(UnresolvableCurrencyPairException::class);
        $service->buy($amount, new Currency("EUR"));
    }

    public function testSellNotDefinedFromCurrency(): void
    {
        $eur = CurrencyValueObject::Create("EUR", "Euro", 2, 978);
        $gbp = CurrencyValueObject::Create("GBP", "Pound Sterling", 2, 826);
        $eurToGbp = new ExchangeRate($eur, $gbp, 1.5678);
        $gbpToEur = new ExchangeRate($gbp, $eur, 1.5432);

        $exchangeRateRepository = new ExchangeRateRepository();
        $exchangeRateRepository->add($eurToGbp);
        $exchangeRateRepository->add($gbpToEur);

        $fee = 0.05;
        $service = new ExchangeOfficeService($exchangeRateRepository->getAll(), $fee);

        $amount = new Money(400, new Currency("USD"));

        $this->expectException(UnresolvableCurrencyPairException::class);
        $service->sell($amount, new Currency("GBP"));
    }

    public function testSellNotDefinedToCurrency()
    {
        $eur = CurrencyValueObject::Create("EUR", "Euro", 2, 978);
        $gbp = CurrencyValueObject::Create("GBP", "Pound Sterling", 2, 826);
        $eurToGbp = new ExchangeRate($eur, $gbp, 1.5678);
        $gbpToEur = new ExchangeRate($gbp, $eur, 1.5432);

        $exchangeRateRepository = new ExchangeRateRepository();
        $exchangeRateRepository->add($eurToGbp);
        $exchangeRateRepository->add($gbpToEur);

        $fee = 0.07;
        $service = new ExchangeOfficeService($exchangeRateRepository->getAll(), $fee);

        $amount = new Money(100, new Currency("EUR"));
        $toCurrency = "USD";

        $this->expectException(UnresolvableCurrencyPairException::class);
        $service->sell($amount, new Currency($toCurrency));
    }
}