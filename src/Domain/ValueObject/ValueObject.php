<?php

namespace IFX\Domain\ValueObject;

abstract class ValueObject
{
    abstract public function isEqual(ValueObject $entity): bool;
}