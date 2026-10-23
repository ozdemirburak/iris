<?php

namespace OzdemirBurak\Iris\Tests;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Color\Cmyk;
use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hexa;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Hsv;
use OzdemirBurak\Iris\Color\Oklch;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Color\Rgba;
use OzdemirBurak\Iris\Helpers\DefinedColor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class PrecisionTest extends TestCase
{
    #[DataProvider('colorsThatConvertWithRounding')]
    #[Group('precision')]
    public function testOperationsWithZeroAmountKeepTheColor(BaseColor $color)
    {
        $expected = (string) $color;
        foreach (['saturate', 'desaturate', 'lighten', 'darken', 'brighten', 'spin'] as $operation) {
            $this->assertEquals($expected, (string) $color->{$operation}(0), "$operation(0)");
            $this->assertEquals($expected, (string) $color->{$operation}(NAN), "$operation(NAN)");
        }
        $this->assertEquals($expected, (string) $color->mix(new Hex('#123456'), 0), 'mix(0)');
        // 100% gives the other color completely, its alpha too
        $this->assertEquals((string) (new Hex('#123456'))->toRgba()->back($color), (string) $color->mix(new Hex('#123456'), 100), 'mix(100)');
    }

    public static function colorsThatConvertWithRounding(): array
    {
        return [
            'oklch out of the sRGB gamut' => [new Oklch('oklch(70% 0.4 150)')],
            'oklch' => [new Oklch('oklch(70% 0.15 150)')],
            'cmyk' => [new Cmyk('5,51,18,11')],
            'hsv' => [new Hsv('90.9,87.6%,94.6%')],
            'hsla' => [new Hsla('203,45%,43%,0.5')],
        ];
    }

    #[Group('precision')]
    public function testHsvOperationsRoundOnlyOnce()
    {
        // A full turn goes through HSL without rounding, so the color comes back exactly
        $this->assertEquals('hsv(90.9,87.6%,94.6%)', (string) (new Hsv('90.9,87.6%,94.6%'))->spin(360));
    }

    #[Group('precision')]
    public function testOklchFromRgbIsExact()
    {
        $this->assertEquals('rgb(0,184,193)', (string) (new Rgb('0,184,193'))->toOklch()->toRgb());
        // The hue 359.996 is shown rounded, as 0 and not as 360
        $oklch = (new Rgb('48,1,21'))->toOklch();
        $this->assertSame(0.0, $oklch->hue());
        $this->assertEquals('rgb(48,1,21)', (string) $oklch->toRgb());
    }

    #[Group('precision')]
    public function testCmykKeepsOneDecimal()
    {
        $this->assertEquals('cmyk(0,60.6,84.6,3.5)', (string) (new Rgb('246,97,38'))->toCmyk());
        $this->assertEquals('rgb(246,97,38)', (string) (new Rgb('246,97,38'))->toCmyk()->toRgb());
        $this->assertEquals('cmyk(0,0,0,100)', (string) (new Rgb('0,0,0'))->toCmyk());
    }

    #[Group('precision')]
    public function testNoFloatNoise()
    {
        $this->assertEquals('hsl(0,50%,0.01%)', (string) (new Hsl('0,50%,50%'))->darken(49.99));
        $this->assertSame(7.1, (new Hsl('0,7%,50%'))->saturate(0.1)->saturation());
    }

    #[Group('precision')]
    public function testOperationsWithNanKeepTheColor()
    {
        $rgba = new Rgba('10,20,30,0.5');
        $this->assertEquals('rgba(10,20,30,0.5)', (string) $rgba->fade(NAN));
        $this->assertEquals('rgba(10,20,30,0.5)', (string) $rgba->fadeIn(NAN));
        $this->assertEquals('rgba(10,20,30,0.5)', (string) $rgba->mix(new Hex('#fff'), NAN));
    }

    #[Group('precision')]
    public function testColorNamesAreExactInEveryModel()
    {
        foreach (DefinedColor::get() as $name => [$hex, $rgb]) {
            $this->assertEquals("#$hex", (string) (new Rgb($rgb))->toHex(), "$name: the rgb column");
            foreach ([Hex::class, Hexa::class, Rgb::class, Rgba::class, Hsl::class, Hsla::class, Hsv::class, Cmyk::class] as $class) {
                $this->assertEquals("#$hex", (string) (new $class($name))->toHex(), "$name as $class");
            }
        }
    }

    #[Group('immutability')]
    public function testBackDoesNotChangeTheColor()
    {
        $rgba = new Rgba('10,20,30,0.5');
        $before = (string) $rgba->toHex();
        $result = $rgba->back((new Rgba('1,2,3,0.5'))->background(new Rgb('0,0,0')));
        $this->assertNotSame($rgba, $result);
        $this->assertEquals($before, (string) $rgba->toHex());
    }

    #[Group('immutability')]
    public function testBackgroundIsCopied()
    {
        $black = new Rgb('0,0,0');
        $rgba = (new Rgba('255,255,255,0.5'))->background($black);
        $black->red(255);
        $rgba->background()->green(255);
        $this->assertEquals('rgb(128,128,128)', (string) $rgba->toRgb());
        $this->assertEquals('rgb(0,0,0)', (string) $rgba->clone()->background());
    }

    #[Group('immutability')]
    public function testConversionToTheSameModelGivesACopy()
    {
        $colors = [new Hex('#abc'), new Hexa('#abcd'), new Rgb('1,2,3'), new Rgba('1,2,3,0.5'), new Hsl('1,2,3'),
            new Hsla('1,2,3,0.5'), new Hsv('1,2,3'), new Cmyk('1,2,3,4'), new Oklch('oklch(50% 0.1 20)')];
        foreach ($colors as $color) {
            $copy = $color->{'to' . $color->getColorModelName($color)}();
            $this->assertNotSame($color, $copy, $color::class);
            $this->assertEquals($color, $copy, $color::class);
        }
    }
}
