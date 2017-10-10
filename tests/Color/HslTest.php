<?php

namespace OzdemirBurak\Iris\Tests\Color;

use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Hsv;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Color\Rgba;
use OzdemirBurak\Iris\Exceptions\InvalidColorException;
use PHPUnit\Framework\TestCase;

class HslTest extends TestCase
{
    /**
     * @group rgb-construction
     */
    public function testDigitString()
    {
        $hsl = new Hsl('hsl(300,100%,50%)');
        $this->validateFuchsia($hsl);
    }

    /**
     * @group rgb-construction
     */
    public function testPredefinedString()
    {
        $hsl = new Hsl('FUCHSIA');
        $this->validateFuchsia($hsl);
    }

    /**
     * @group rgb-construction
     */
    public function testInvalidColor()
    {
        try {
            new Hsl('333,0,666');
        } catch (InvalidColorException $e) {
            $this->assertContains('Invalid HSL value', $e->getMessage());
            return;
        }
        $this->fail('Exception has not been raised.');
    }

    /**
     * @group rgb-construction
     */
    public function testGarbageColor()
    {
        try {
            new Hsl('ThisIsAnInvalidValue');
        } catch (InvalidColorException $e) {
            $this->assertContains('Invalid HSL value', $e->getMessage());
            return;
        }
        $this->fail('Exception has not been raised.');
    }

    /**
     * @param \OzdemirBurak\Iris\Color\Hsl $hsl
     */
    private function validateFuchsia(Hsl $hsl)
    {
        $this->assertEquals('300', $hsl->hue());
        $this->assertEquals('100', $hsl->saturation());
        $this->assertEquals('50', $hsl->lightness());
        $this->assertEquals([300, '100%', '50%'], $hsl->values());
        $this->assertEquals([300/360, 100/100, 50/100], $hsl->valuesInUnitInterval());
        $this->assertEquals('hsl(300,100%,50%)', $hsl);
        $this->assertEquals(new Hex('ff00ff'), $hsl->toHex());
        $this->assertEquals(new Hsl('300,100,50'), $hsl->toHsl());
        $this->assertEquals(new Hsla('300,100,50,1.0'), $hsl->toHsla());
        $this->assertEquals(new Hsv('300,100,100'), $hsl->toHsv());
        $this->assertEquals(new Rgb('255,0,255'), $hsl->toRgb());
        $this->assertEquals(new Rgba('255,0,255,1.0'), $hsl->toRgba());
    }
}
