<?php

namespace OzdemirBurak\Iris\Tests;

use OzdemirBurak\Iris\Color\Cmyk;
use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hexa;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Oklch;
use OzdemirBurak\Iris\Color\Rgba;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsv;
use OzdemirBurak\Iris\Color\Rgb;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class SettersTest extends TestCase
{
    #[Group('setters')]
    public function testRgbSettersClamp()
    {
        $rgb = new Rgb('10,20,30');
        $this->assertEquals('rgb(255,0,30)', (string) $rgb->red(300)->green(-5));
        $this->assertSame(128, $rgb->blue(127.5)->blue());
    }

    #[Group('setters')]
    public function testHueWraps()
    {
        $hsl = new Hsl('10,50,50');
        $this->assertEquals(40, $hsl->hue(400)->hue());
        $this->assertEquals(330, $hsl->hue(-30)->hue());
        $this->assertEquals(0, $hsl->hue(360)->hue());
        $this->assertEquals(0, $hsl->hue(-360)->hue());
        $this->assertEquals('hsl(0,50%,50%)', (string) $hsl, 'The hue -360 must not print as "-0"');
    }

    #[Group('setters')]
    public function testPercentageSettersClamp()
    {
        $hsl = new Hsl('10,50,50');
        $this->assertEquals('hsl(10,100%,0%)', (string) $hsl->saturation(150)->lightness(-5));

        $hsv = new Hsv('10,50,50');
        $this->assertEquals('hsv(10,0%,100%)', (string) $hsv->saturation(-1)->value(120));

        $cmyk = new Cmyk('10,20,30,40');
        $this->assertEquals('cmyk(100,0,30,100)', (string) $cmyk->cyan(150)->magenta(-10)->black(101));
    }

    #[Group('setters')]
    public function testHueHasNoFloatNoise()
    {
        $this->assertEquals('hsl(0.1,50%,50%)', (string) (new Hsl('0,50,50'))->hue(0.1));
        $this->assertEquals('hsl(0.9,100%,32.4%)', (string) (new Rgb('165,0,80'))->toHsl()->spin(30));
        $this->assertEquals('hsl(359.9,50%,50%)', (string) (new Hsl('0,50,50'))->hue(-0.1));
        $this->assertEquals('hsl(0,50%,50%)', (string) (new Hsl('0,50,50'))->hue(359.99999999999));
    }

    #[Group('setters')]
    public function testInvalidNumbersAreIgnored()
    {
        $rgb = new Rgb('16,32,48');
        $this->assertEquals('rgb(16,32,48)', (string) $rgb->red('zz')->green('')->blue(NAN));
        $this->assertEquals('rgb(16,32,48)', (string) $rgb->red(INF));
        $hsl = new Hsl('10,20,30');
        $this->assertEquals('hsl(10,20%,30%)', (string) $hsl->hue(NAN)->saturation(INF)->lightness(-INF));
        $cmyk = new Cmyk('1,2,3,4');
        $this->assertEquals('cmyk(1,2,3,4)', (string) $cmyk->cyan(NAN));
    }

    #[Group('setters')]
    public function testNonNumericValueIsIgnored()
    {
        // A setter with a value always returns the color, so that calls can be chained
        $hsl = new Hsl('10,50,50');
        $this->assertSame($hsl, $hsl->hue('abc'));
        $this->assertEquals('hsl(10,50%,50%)', (string) $hsl->saturation('50%')->lightness(''));
        $this->assertEquals('hsv(10,50%,50%)', (string) (new Hsv('10,50,50'))->value('x'));
        $this->assertEquals('cmyk(1,2,3,4)', (string) (new Cmyk('1,2,3,4'))->yellow('y'));
    }

    #[Group('setters')]
    public function testAlphaIgnoresInvalidNumbers()
    {
        foreach ([new Rgba('1,2,3,0.5'), new Hsla('1,2,3,0.5'), new Hexa('#01020380'), new Oklch('oklch(50% 0.1 20 / 0.5)')] as $color) {
            $before = (string) $color;
            $this->assertSame($before, (string) $color->alpha(NAN)->alpha(INF)->alpha('abc')->alpha(''), $color::class);
        }
        $this->assertEquals(0.25, (new Rgba('1,2,3,0.5'))->alpha('0.25')->alpha());
    }

    #[Group('setters')]
    public function testHexSettersTakeHexDigits()
    {
        $hex = new Hex('#0a141e');
        $this->assertEquals('#ff141e', (string) $hex->red('ff'));
        $this->assertEquals('#ff551e', (string) $hex->green('5'), 'One digit is doubled');
        $this->assertEquals('#ff551e', (string) $hex->blue('zz')->blue(10.6)->blue(300), 'Invalid values are ignored');
        $this->assertEquals('#0a141e80', (string) (new Hexa('#0a141e80'))->red('0a'));
    }
}
