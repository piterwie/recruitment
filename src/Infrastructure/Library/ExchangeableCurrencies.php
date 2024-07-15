<?php

namespace IFX\Infrastructure\Library;

use IFX\Domain\ValueObject\CurrencyValueObject;
use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use Traversable;

class ExchangeableCurrencies implements Currencies
{
    private array $currencies;

    public function addCurrency(CurrencyValueObject $currency): void
    {
        $this->currencies[$currency->getAlphabeticCode()] = $currency->getInfo();
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator(
            array_map(
                function ($code) {
                    return new Currency($code);
                },
                array_keys($this->currencies)
            )
        );
    }

    public function contains(Currency $currency): bool
    {
        return isset($this->currencies[$currency->getCode()]);
    }

    public function subunitFor(Currency $currency)
    {
        if (!$this->contains($currency)) {
            throw new UnknownCurrencyException('CurrencyValueObject not defined ' . $currency->getCode());
        }

        return $this->currencies[$currency->getCode()]['minorUnit'];
    }
}