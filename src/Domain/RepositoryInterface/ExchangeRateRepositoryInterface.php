<?php

namespace IFX\Domain\RepositoryInterface;

use IFX\Domain\Entity\ExchangeRate;

interface ExchangeRateRepositoryInterface
{
    public function add(ExchangeRate $currency): void;

    public function get(string $id): ExchangeRate;
}