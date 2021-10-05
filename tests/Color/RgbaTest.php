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

class RgbaTest extends TestCase
{
    /**
     * @group rgba-construction
     */
    public function testDigitString()
    {
        $rgba = new Rgba('rgba(255,0,0,0.3)');
        $this->assertEquals(255, $rgba->red());
        $this->assertEquals(0, $rgba->blue());
        $this->assertEquals(0, $rgba->green());
        $this->assertEquals(0.3, $rgba->alpha());
        $this->assertEquals([255, 0, 0, 0.3], $rgba->values());
    }

    /**
     * @group rgba-construction
     */
    public function testPredefinedString()
    {
        $rgba = new Rgba('FUCHSIA');
        $this->validateFuchsia($rgba);
    }

    /**
     * @dataProvider invalidColors
     * @group rgba-construction
     * @param string $colorDefinition
     */
    public function testInvalidColorDefinitionsMustThrow($colorDefinition)
    {
        $this->expectException(\OzdemirBurak\Iris\Exceptions\InvalidColorException::class);
        $this->expectExceptionMessage('Invalid RGBA value');
        new Rgba($colorDefinition);
    }

    public function invalidColors()
    {
        return [
            ['rgba(255,0,0,1.2)'],
            ['rgba(255,0,0,1.2,0.2.3)'], // Invalid string.
            ['ThisIsAnInvalidValue'],
        ];
    }

    /**
     * @group rgba-conversion
     */
    public function testRgbaConversion()
    {
        $rgba = new Rgba('rgba(11,22,33,0.2)');
        $this->assertEquals(new Hex('ced0d2'), $rgba->toHex());
        $this->assertEquals(new Hexa('ced0d233'), $rgba->toHexa());
        $rgba = new Rgba('rgba(93,111,222,0.33)');
        $this->assertEquals(new Hex('a7add1'), $rgba->background((new Hex('ccc'))->toRgb())->toHex());
    }

    /**
     * @param \OzdemirBurak\Iris\Color\Rgba $rgba
     */
    private function validateFuchsia(Rgba $rgba)
    {
        $this->assertEquals(255, $rgba->red());
        $this->assertEquals(0, $rgba->green());
        $this->assertEquals(255, $rgba->blue());
        $this->assertEquals(1.0, $rgba->alpha());
        $this->assertEquals([255, 0, 255, 1.0], $rgba->values());
        $this->assertEquals('rgba(255,0,255,1)', $rgba->toRgba()->__toString());
        $this->assertEquals(new Hex('ff00ff'), $rgba->toHex());
        $this->assertEquals(new Hexa('ff00ffff'), $rgba->toHexa());
        $this->assertEquals(new Hsl('300,100,50'), $rgba->toHsl());
        $this->assertEquals(new Hsla('300,100,50,1.0'), $rgba->toHsla());
        $this->assertEquals(new Hsv('300,100,100'), $rgba->toHsv());
        $this->assertEquals(new Rgb('255,0,255'), $rgba->toRgb());
        $this->assertEquals(new Rgba('255,0,255,1.0'), $rgba->toRgba());
    }
}
