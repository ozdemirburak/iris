<?php

namespace OzdemirBurak\Iris\Tests\Color;

use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hexa;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Hsv;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Color\Rgba;
use PHPUnit\Framework\TestCase;

class HexaTest extends TestCase
{
    /**
     * @group hexa-construction
     */
    public function testDigitString()
    {
        $hexa = new Hexa('#ff00ff99');
        $this->validateNonAlpha($hexa);
        $this->validateAlpha($hexa, '99', 0.6);
    }

    /**
     * @group hexa-construction
     */
    public function testPredefinedString()
    {
        $hexa = new Hexa('FUCHSIA');
        $this->validateNonAlpha($hexa);
        $this->validateAlpha($hexa, 'ff', 1);
    }

    /**
     * @group hex-construction
     */
    public function testInvalidColor()
    {
        $this->expectException(\OzdemirBurak\Iris\Exceptions\InvalidColorException::class);
        $this->expectExceptionMessage('Invalid HEXA value');
        new Hexa('6F87DEZ');
    }

    /**
     * @param \OzdemirBurak\Iris\Color\Hexa $hexa
     */
    private function validateNonAlpha(Hexa $hexa)
    {
        $this->assertEquals('ff', $hexa->red());
        $this->assertEquals('00', $hexa->green());
        $this->assertEquals('ff', $hexa->blue());
        $this->assertEquals(new Hex('ff00ff'), $hexa->toHex());
        $this->assertEquals(new Hsl('300,100,50'), $hexa->toHsl());
        $this->assertEquals(new Hsv('300,100,100'), $hexa->toHsv());
        $this->assertEquals(new Rgb('255,0,255'), $hexa->toRgb());
    }

    /**
     * @param Hexa $hexa
     * @param string $expectedAlphaHex
     * @param float $expectedAlphaFloat
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     */
    private function validateAlpha(Hexa $hexa, string $expectedAlphaHex, float $expectedAlphaFloat)
    {
        $this->assertEquals($expectedAlphaFloat, $hexa->alpha());
        $this->assertEquals("#ff00ff{$expectedAlphaHex}", (string)$hexa);
        $this->assertEquals(['ff', '00', 'ff', $expectedAlphaFloat], $hexa->values());
        $this->assertEquals(new Rgba("255,0,255,{$expectedAlphaFloat}"), $hexa->toRgba());
        $this->assertEquals(new Hsla("300,100,50,{$expectedAlphaFloat}"), $hexa->toHsla());
    }
}
