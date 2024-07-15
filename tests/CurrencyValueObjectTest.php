<?php

namespace Tests;

use IFX\Domain\ValueObject\CurrencyValueObject;
use PHPUnit\Framework\TestCase;

final class CurrencyValueObjectTest extends TestCase
{
    public function testCreatingCurrencyValueObject(): void
    {
        $alphabeticCode = "GBP";
        $name = "Pound Sterling";
        $minorUnit = 2;
        $numericCode = 826;
        $currency = CurrencyValueObject::Create($alphabeticCode, $name, $minorUnit, $numericCode);

        $this->assertEquals($alphabeticCode, $currency->getAlphabeticCode());
        $this->assertEquals($name, $currency->getName());
        $this->assertEquals($minorUnit, $currency->getMinorUnit());
        $this->assertEquals($numericCode, $currency->getNumericCode());

        $info = $currency->getInfo();
        $this->assertArrayHasKey("alphabeticCode", $info);
        $this->assertEquals($alphabeticCode, $info["alphabeticCode"]);
        $this->assertArrayHasKey("currency", $info);
        $this->assertEquals($name, $info["currency"]);
        $this->assertArrayHasKey("minorUnit", $info);
        $this->assertEquals($minorUnit, $info["minorUnit"]);
        $this->assertArrayHasKey("numericCode", $info);
        $this->assertEquals($numericCode, $info["numericCode"]);
    }
}