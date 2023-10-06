<?php

namespace OzdemirBurak\Iris\Tests\Color;

use OzdemirBurak\Iris\Color\Cmyk;
use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hexa;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Hsv;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Color\Rgba;
use PHPUnit\Framework\TestCase;

class RgbTest extends TestCase
{
    /**
     * @group rgb-construction
     */
    public function testDigitString()
    {
        $rgb = new Rgb('rgb(255, 0, 255)');
        $this->validateFuchsia($rgb);
    }

    /**
     * @group rgb-construction
     */
    public function testPredefinedString()
    {
        $rgb = new Rgb('FUCHSIA');
        $this->validateFuchsia($rgb);
    }

    /**
     * @group rgb-construction
     */
    public function testInvalidColor()
    {
        $this->expectException(\OzdemirBurak\Iris\Exceptions\InvalidColorException::class);
        $this->expectExceptionMessage('Invalid RGB value');
        new Rgb('333,0,666');
    }


    /**
     * @group rgb-construction
     */
    public function testGarbageColor()
    {
        $this->expectException(\OzdemirBurak\Iris\Exceptions\InvalidColorException::class);
        $this->expectExceptionMessage('Invalid RGB value');
        new Rgb('ThisIsAnInvalidValue');
    }

    /**
     * @param \OzdemirBurak\Iris\Color\Rgb $rgb
     */
    private function validateFuchsia(Rgb $rgb)
    {
        $this->assertEquals(255, $rgb->red());
        $this->assertEquals(0, $rgb->green());
        $this->assertEquals(255, $rgb->blue());
        $this->assertEquals([255, 0, 255], $rgb->values());
        $this->assertEquals('rgb(255,0,255)', $rgb);
        $this->assertEquals(new Hex('ff00ff'), $rgb->toHex());
        $this->assertEquals(new Hexa('ff00ffff'), $rgb->toHexa());
        $this->assertEquals(new Hsl('300,100,50'), $rgb->toHsl());
        $this->assertEquals(new Hsla('300,100,50,1.0'), $rgb->toHsla());
        $this->assertEquals(new Hsv('300,100,100'), $rgb->toHsv());
        $this->assertEquals(new Rgb('255,0,255'), $rgb->toRgb());
        $this->assertEquals(new Rgba('255,0,255,1.0'), $rgb->toRgba());
        $this->assertEquals(new Cmyk('0,100,0,0'), $rgb->toCmyk());
    }
}
