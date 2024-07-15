<?php

namespace IFX\Infrastructure\Service;

use IFX\Domain\Entity\ExchangeRate;
use IFX\Domain\ValueObject\ExchangeRateValueObject;
use IFX\Infrastructure\Library\ExchangeableCurrencies;
use IFX\Infrastructure\Service\Exceptions\InvalidCurrencyException;
use IFX\Infrastructure\Service\Exceptions\InvalidFeeException;
use Money\Converter;
use Money\Currency;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange\FixedExchange;
use Money\Money;


readonly class ExchangeOfficeService
{
    private Converter $converter;
    private float $fee;
    private array $exchangeTable;

    /**
     * @throws InvalidFeeException
     */
    public function __construct(array $rates, float $fee)
    {
        if ($fee > 1) {
            throw new InvalidFeeException("Fee cannot be greater than 1");
        }
        if ($fee < 0) {
            throw new InvalidFeeException("Fee cannot be lower than 0");
        }
        $this->fee = $fee;

        $exchangeTable = [];
        $exCurrencies = new ExchangeableCurrencies();
        foreach ($rates as $rate) {
            /** @var ExchangeRate $rate */
            $exCurrencies->addCurrency($rate->getFrom());
            $exCurrencies->addCurrency($rate->getTo());
            $exchangeTable[$rate->getFrom()->getAlphabeticCode()] = [$rate->getTo()->getAlphabeticCode() => $rate->getRate()];
        }
        $this->exchangeTable = $exchangeTable;
        $exchange = new FixedExchange($this->exchangeTable);
        $this->converter = new Converter($exCurrencies, $exchange);
    }

    public function buy(Money $amount, Currency $toCurrency): Money
    {
        $afterFee = $amount->multiply(1 - $this->fee);

        return $this->convertAmount($afterFee, $toCurrency);
    }

    /**
     * @throws UnresolvableCurrencyPairException
     */
    public function sell(Money $amount, Currency $from): Money
    {
        $to = $amount->getCurrency();
        if (!key_exists($from->getCode(), $this->exchangeTable)) {
            throw new UnresolvableCurrencyPairException(sprintf("Cannot resolve a currency pair for currencies: %s/%s", $from->getCode(), $to->getCode()));
        }
        if (!key_exists($to->getCode(), $this->exchangeTable[$from->getCode()])) {
            throw new UnresolvableCurrencyPairException(sprintf("Cannot resolve a currency pair for currencies: %s/%s", $from->getCode(), $to->getCode()));
        }
        $ratio = $this->exchangeTable[$from->getCode()][$to->getCode()];

        $toFee = new Money($amount->getAmount(), $from);
        $toFee = $toFee->divide($ratio);

        return $toFee->multiply(1 - $this->fee);
    }

    private function convertAmount(Money $fromAmount, Currency $toCurrency): Money
    {
        return $this->converter->convert($fromAmount, $toCurrency);
    }
}
