<?php

namespace IFX\Domain\ValueObject;

final class CurrencyValueObject extends ValueObject
{
    private string $alphabeticCode;
    private string $name;
    private int $minorUnit;
    private int $numericCode;

    private function __construct(string $alphabeticCode, string $name, int $minorUnit, int $numericCode)
    {
        $this->alphabeticCode = $alphabeticCode;
        $this->name = $name;
        $this->minorUnit = $minorUnit;
        $this->numericCode = $numericCode;
    }

    public static function Create(string $alphabeticCode, string $name, int $minorUnit, int $numericCode): self
    {
        return new self($alphabeticCode, $name, $minorUnit, $numericCode);
    }

    public function getAlphabeticCode(): string
    {
        return $this->alphabeticCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMinorUnit(): int
    {
        return $this->minorUnit;
    }

    public function getNumericCode(): int
    {
        return $this->numericCode;
    }

    public function getInfo(): array
    {
        return [
            "alphabeticCode" => $this->alphabeticCode,
            "currency" => $this->name,
            "minorUnit" => $this->minorUnit,
            "numericCode" => $this->numericCode,
        ];
    }

    public function isEqual(ValueObject $entity): bool
    {
        return $this->alphabeticCode === $entity->alphabeticCode &&
            $this->numericCode === $entity->numericCode &&
            $this->name === $entity->name &&
            $this->minorUnit === $entity->minorUnit;
    }
}