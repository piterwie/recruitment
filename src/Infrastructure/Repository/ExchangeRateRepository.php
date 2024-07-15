<?php

namespace IFX\Infrastructure\Repository;

use IFX\Domain\Entity\ExchangeRate;
use IFX\Domain\Exception\ExchangeRateNotFound;
use IFX\Domain\RepositoryInterface\ExchangeRateRepositoryInterface;

class ExchangeRateRepository extends Repository implements ExchangeRateRepositoryInterface
{
    public function get(string $id): ExchangeRate
    {
        if (!$this->exists($id)) {
            throw new ExchangeRateNotFound();
        }
        return $this->objects[$id];
    }
}
