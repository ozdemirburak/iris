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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class RgbaTest extends TestCase
{
    #[Group('rgba-construction')]
    public function testDigitString()
    {
        $rgba = new Rgba('rgba(255,0,0,0.3)');
        $this->assertEquals(255, $rgba->red());
        $this->assertEquals(0, $rgba->blue());
        $this->assertEquals(0, $rgba->green());
        $this->assertEquals(0.3, $rgba->alpha());
        $this->assertEquals([255, 0, 0, 0.3], $rgba->values());
    }

    #[Group('rgba-construction')]
    public function testPredefinedString()
    {
        $rgba = new Rgba('FUCHSIA');
        $this->validateFuchsia($rgba);
    }

    #[DataProvider('invalidColors')]
    #[Group('rgba-construction')]
    public function testInvalidColorDefinitionsMustThrow($colorDefinition)
    {
        $this->expectException(\OzdemirBurak\Iris\Exceptions\InvalidColorException::class);
        $this->expectExceptionMessage('Invalid RGBA value');
        new Rgba($colorDefinition);
    }

    public static function invalidColors(): array
    {
        return [
            ['rgba(255,0,0,1.2)'],
            ['rgba(255,0,0,1.2,0.2.3)'], // Invalid string.
            ['ThisIsAnInvalidValue'],
            ['1,2'], // Too few values, must not trigger "Undefined array key" warnings.
        ];
    }

    #[Group('rgba-conversion')]
    public function testRgbaConversion()
    {
        $rgba = new Rgba('rgba(11,22,33,0.2)');
        // toHex applies alpha compositing against white background
        $this->assertEquals(new Hex('ced0d3'), $rgba->toHex());
        // toHexa preserves original RGB values with alpha channel
        $this->assertEquals(new Hexa('0b162133'), $rgba->toHexa());
        $rgba = new Rgba('rgba(93,111,222,0.33)');
        $this->assertEquals(new Hex('a7add2'), $rgba->background((new Hex('ccc'))->toRgb())->toHex());
    }

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
        $this->assertEquals(new Cmyk('0,100,0,0'), $rgba->toCmyk());
    }

    #[Group('rgba-setters')]
    public function testSetters()
    {
        $rgba = new Rgba('rgba(10,20,30,0.5)');
        $this->assertEquals('rgba(100,20,30,0.5)', (string) $rgba->red(100));
        $this->assertEquals('rgba(100,5,30,0.5)', (string) $rgba->green(5));
        $this->assertEquals('rgba(100,5,0,0.5)', (string) $rgba->blue(0));
        $this->assertEquals('rgba(255,5,0,0.5)', (string) $rgba->red(300), 'Out of range values are clamped');
        $this->assertEquals('rgba(0,5,0,0.5)', (string) $rgba->red(-20));
        $this->assertSame(100, $rgba->red(100)->red());
        // Composited onto white: 0.7 * 255 + 0.3 * 200 = 238.5 is rounded to 239, and so on
        $this->assertEquals(new Hex('#efd1e0'), (new Rgba('rgba(10,20,30,0.3)'))->red(200)->green(100)->blue(150)->toHex());
    }

    #[Group('rgba-construction')]
    public function testAlphaWithoutLeadingZero()
    {
        $this->assertEquals('rgba(0,0,0,0.5)', (string) new Rgba('rgba(0,0,0,.5)'));
    }

    #[Group('rgba-conversion')]
    public function testCompositingOntoTheSameColorKeepsIt()
    {
        $rgba = (new Rgba('rgba(3,3,3,0.01)'))->background(new Rgb('3,3,3'));
        $this->assertEquals(new Rgb('3,3,3'), $rgba->toRgb());
    }

    #[Group('rgba-conversion')]
    public function testToHslaWithTinyAlpha()
    {
        $this->assertEquals('hsla(210,50%,7.8%,0)', (string) (new Rgba('rgba(10,20,30,0.5)'))->alpha(0.00001)->toHsla());
    }
}
