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

class HslaTest extends TestCase
{
    /**
     * @group hsla-construction
     */
    public function testDigitString()
    {
        $hsla = new Hsla('hsla(150,100%,50%,0.3)');
        $this->assertEquals('150', $hsla->hue());
        $this->assertEquals('100', $hsla->saturation());
        $this->assertEquals('50', $hsla->lightness());
        $this->assertEquals(0.3, $hsla->alpha());
        $this->assertEquals([150, '100%', '50%', 0.3], $hsla->values());
        $hsla->alpha(0);
        $this->assertEquals(0, $hsla->alpha());
        $this->assertEquals('hsla(150,100%,50%,0)', $hsla->__toString());
    }

    /**
     * @group hsla-construction
     */
    public function testPredefinedString()
    {
        $hsla = new Hsla('FUCHSIA');
        $this->assertEquals('300', $hsla->hue());
        $this->assertEquals('100', $hsla->saturation());
        $this->assertEquals('50', $hsla->lightness());
        $this->assertEquals(1, $hsla->alpha());
        $this->assertEquals([300, '100%', '50%', 1], $hsla->values());
    }

    /**
     * @group hsla-construction
     */
    public function testFuchsiaString()
    {
        $hsla = new Hsla('FUCHSIA');
        $this->validateFuchsia($hsla);
    }

    /**
     * @group hsla-construction
     */
    public function testInvalidColor()
    {
        $this->expectException(\OzdemirBurak\Iris\Exceptions\InvalidColorException::class);
        $this->expectExceptionMessage('Invalid HSLA value');
        new Hsla('hsla(150,100%,50%,0.3.3,4)');
    }

    /**
     * @group hsla-construction
     */
    public function testGarbageColor()
    {
        $this->expectException(\OzdemirBurak\Iris\Exceptions\InvalidColorException::class);
        $this->expectExceptionMessage('Invalid HSLA value');
        new Hsla('hsla(361,1%,1%,0.3)');
    }

    /**
     * @group hsla-conversion
     */
    public function testHslaConversion()
    {
        $hsla = new Hsla('hsla(150,100%,50%,0.3)');
        $this->assertEquals(new Hex('b2ffd8'), $hsla->toHex());
        $this->assertEquals(new Rgba('0,255,128,0.3'), $hsla->toRgba());
        $this->assertEquals(new Hexa('00ff804c'), $hsla->toHexa());
    }


    /**
     * @param \OzdemirBurak\Iris\Color\Hsla $hsla
     */
    private function validateFuchsia(Hsla $hsla)
    {
        $this->assertEquals(new Hex('ff00ff'), $hsla->toHex());
        $this->assertEquals(new Hexa('ff00ffff'), $hsla->toHexa());
        $this->assertEquals(new Hsl('300,100,50'), $hsla->toHsl());
        $this->assertEquals(new Hsla('300,100,50,1.0'), $hsla->toHsla());
        $this->assertEquals(new Hsv('300,100,100'), $hsla->toHsv());
        $this->assertEquals(new Rgb('255,0,255'), $hsla->toRgb());
        $this->assertEquals(new Rgba('255,0,255,1.0'), $hsla->toRgba());
    }
}
