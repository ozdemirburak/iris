<?php

namespace OzdemirBurak\Iris\Tests;

use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Rgba;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class RoundTripTest extends TestCase
{
    /**
     * @return \Generator<\OzdemirBurak\Iris\Color\Hex>
     */
    private static function sampleColors(): \Generator
    {
        for ($r = 0; $r <= 255; $r += 15) {
            for ($g = 0; $g <= 255; $g += 15) {
                for ($b = 0; $b <= 255; $b += 15) {
                    yield new Hex(sprintf('%02x%02x%02x', $r, $g, $b));
                }
            }
        }
    }

    #[Group('round-trip')]
    public function testHslAndHsvRoundTrips()
    {
        foreach (self::sampleColors() as $hex) {
            $expected = (string) $hex;
            $this->assertEquals($expected, (string) $hex->toHsl()->toHex(), "$expected via hsl");
            $this->assertEquals($expected, (string) $hex->toHsv()->toHex(), "$expected via hsv");
            $this->assertEquals($expected, (string) $hex->toHsv()->toHsl()->toHex(), "$expected via hsv and hsl");
            $this->assertEquals($expected, (string) $hex->toHsla()->toRgba()->toHex(), "$expected via hsla and rgba");
            $this->assertEquals($expected, (string) $hex->toCmyk()->toHex(), "$expected via cmyk");
            $this->assertEquals($expected, (string) $hex->toOklch()->toHex(), "$expected via oklch");
        }
    }

    #[Group('round-trip')]
    public function testNeutralOperationsKeepTheColor()
    {
        foreach (self::sampleColors() as $hex) {
            $expected = (string) $hex;
            $this->assertEquals($expected, (string) $hex->spin(0), "$expected spin(0)");
            $this->assertEquals($expected, (string) $hex->toHsv()->lighten(0)->toHex(), "$expected hsv lighten(0)");
            $rgba = new Rgba(implode(',', $hex->toRgb()->values()) . ',0.4');
            $this->assertEquals((string) $rgba, (string) $rgba->spin(0), "$rgba spin(0)");
        }
    }
}
