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

class CmykTest extends TestCase
{
    /**
     * @group cmyk-construction
     */
    public function testDigitString()
    {
        $cmyk = new Cmyk('cmyk(0%, 100%, 0%, 0%)');
        $this->validateFuchsia($cmyk);
    }

    /**
     * @group cmyk-construction
     */
    public function testPredefinedString()
    {
        $cmyk = new Cmyk('FUCHSIA');
        $this->validateFuchsia($cmyk);
    }

    /**
     * @group cmyk-construction
     */
    public function testInvalidColor()
    {
        $this->expectException(\OzdemirBurak\Iris\Exceptions\InvalidColorException::class);
        $this->expectExceptionMessage('Invalid CMYK value');
        new Cmyk('255,0,666,200');
    }


    /**
     * @group cmyk-construction
     */
    public function testGarbageColor()
    {
        $this->expectException(\OzdemirBurak\Iris\Exceptions\InvalidColorException::class);
        $this->expectExceptionMessage('Invalid CMYK value');
        new Cmyk('ThisIsAnInvalidValue');
    }

    /**
     * @param \OzdemirBurak\Iris\Color\Cmyk $cmyk
     */
    private function validateFuchsia(Cmyk $cmyk)
    {
        $this->assertEquals(0, $cmyk->cyan());
        $this->assertEquals(100, $cmyk->magenta());
        $this->assertEquals(0, $cmyk->yellow());
        $this->assertEquals(0, $cmyk->black());
        $this->assertEquals([0, 100, 0, 0], $cmyk->values());
        $this->assertEquals('cmyk(0,100,0,0)', $cmyk);
        $this->assertEquals(new Hex('ff00ff'), $cmyk->toHex());
        $this->assertEquals(new Hexa('ff00ffff'), $cmyk->toHexa());
        $this->assertEquals(new Hsl('300,100,50'), $cmyk->toHsl());
        $this->assertEquals(new Hsla('300,100,50,1.0'), $cmyk->toHsla());
        $this->assertEquals(new Hsv('300,100,100'), $cmyk->toHsv());
        $this->assertEquals(new Rgb('255,0,255'), $cmyk->toRgb());
        $this->assertEquals(new Rgba('255,0,255,1.0'), $cmyk->toRgba());
    }
}
