<?php

namespace OzdemirBurak\Iris\Tests;

use OzdemirBurak\Iris\Color\Cmyk;
use OzdemirBurak\Iris\Color\Factory;
use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hexa;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Hsv;
use OzdemirBurak\Iris\Color\Oklch;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Color\Rgba;
use OzdemirBurak\Iris\Exceptions\InvalidColorException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class CssSyntaxTest extends TestCase
{
    #[DataProvider('validCss')]
    #[Group('css-syntax')]
    public function testFactoryReadsCssColors(string $css, string $class, string $expected)
    {
        $color = Factory::init($css);
        $this->assertInstanceOf($class, $color);
        $this->assertEquals($expected, (string) $color);
    }

    public static function validCss(): array
    {
        return [
            'rgb space syntax' => ['rgb(10 20 30)', Rgb::class, 'rgb(10,20,30)'],
            'rgb with alpha percent' => ['rgb(10 20 30 / 50%)', Rgba::class, 'rgba(10,20,30,0.5)'],
            'rgb with alpha without leading zero' => ['rgb(10 20 30 / .5)', Rgba::class, 'rgba(10,20,30,0.5)'],
            'rgb with comma alpha' => ['rgb(10,20,30,0.5)', Rgba::class, 'rgba(10,20,30,0.5)'],
            'rgb percentages' => ['rgb(100% 0% 50%)', Rgb::class, 'rgb(255,0,128)'],
            'rgb decimals are rounded' => ['rgb(10.4, 20.6, 30)', Rgb::class, 'rgb(10,21,30)'],
            'uppercase function' => ['RGB(1,2,3)', Rgb::class, 'rgb(1,2,3)'],
            'hsl deg' => ['hsl(120deg 100% 50%)', Hsl::class, 'hsl(120,100%,50%)'],
            'hsl turn' => ['hsl(0.5turn 50% 50%)', Hsl::class, 'hsl(180,50%,50%)'],
            'hsl none' => ['hsl(none 0% 50%)', Hsl::class, 'hsl(0,0%,50%)'],
            'hsl with alpha' => ['hsl(120 100% 50% / 0.25)', Hsla::class, 'hsla(120,100%,50%,0.25)'],
            'hsla alpha percent' => ['hsla(120,100%,50%,30%)', Hsla::class, 'hsla(120,100%,50%,0.3)'],
            'hsv space syntax' => ['hsv(120 50% 50%)', Hsv::class, 'hsv(120,50%,50%)'],
            'cmyk percentages' => ['cmyk(10% 20% 30% 40%)', Cmyk::class, 'cmyk(10,20,30,40)'],
            'cmyk decimals' => ['cmyk(12.5,20,30,40)', Cmyk::class, 'cmyk(12.5,20,30,40)'],
            '4-digit hex' => ['#f0f8', Hexa::class, '#ff00ff88'],
            'named color with spaces' => [' red ', Hex::class, '#ff0000'],
            'oklch chroma percent' => ['oklch(70% 40% 150)', Oklch::class, 'oklch(70% 0.16 150)'],
            'oklch with alpha' => ['oklch(0.7 0.1 120deg / 50%)', Oklch::class, 'oklch(70% 0.1 120 / 0.5)'],
            'exponent' => ['rgb(1e2 0 0)', Rgb::class, 'rgb(100,0,0)'],
            'line breaks' => ["rgb(\n  10\n  20\n  30\n)", Rgb::class, 'rgb(10,20,30)'],
            'line break before alpha' => ["rgb(1 2 3 /\n 0.5)", Rgba::class, 'rgba(1,2,3,0.5)'],
            'bare numbers with spaces' => ['10 20 30', Rgb::class, 'rgb(10,20,30)'],
            'bare numbers with spaces and alpha' => ['10 20 30 0.5', Rgba::class, 'rgba(10,20,30,0.5)'],
            'transparent' => ['transparent', Rgba::class, 'rgba(0,0,0,0)'],
            'hue above 360 wraps' => ['hsl(2turn 50% 50%)', Hsl::class, 'hsl(0,50%,50%)'],
            'negative hue wraps' => ['hsl(-120 100% 50%)', Hsl::class, 'hsl(240,100%,50%)'],
            'hue in grad' => ['hsl(100grad 100% 50%)', Hsl::class, 'hsl(90,100%,50%)'],
            'hue in rad' => ['hsl(3.14159265358979rad 100% 50%)', Hsl::class, 'hsl(180,100%,50%)'],
            'plus sign' => ['rgb(255 0 0 / +.5)', Rgba::class, 'rgba(255,0,0,0.5)'],
            'channel rounded before the range check' => ['rgb(255.4 0 0)', Rgb::class, 'rgb(255,0,0)'],
            'form feed is whitespace' => ["\frgb(1 2 3)\f", Rgb::class, 'rgb(1,2,3)'],
        ];
    }

    #[Group('css-syntax')]
    public function testTransparentInAlphaModels()
    {
        $this->assertEquals('#00000000', (string) new Hexa('transparent'));
        $this->assertEquals('hsla(0,0%,0%,0)', (string) new Hsla('TRANSPARENT'));
        $this->expectException(InvalidColorException::class);
        new Hex('transparent');
    }

    #[Group('css-syntax')]
    public function testTinyValuesSurviveTheirStringForm()
    {
        // PHP prints floats below 0.0001 with an exponent, e.g. 1.0E-5, which must parse again
        $hsl = new Hsl('hsl(0.00001 50% 50%)');
        $this->assertEquals('hsla(1.0E-5,50%,50%,0.5)', (string) $hsl->fade(50));
        $this->assertEquals($hsl, new Hsl((string) $hsl));
        $this->assertInstanceOf(Hsla::class, (new Hsla('hsla(0,50%,50%,1)'))->darken(49.99999));
        $this->assertInstanceOf(Hsla::class, (new Hsla('hsla(359,50%,50%,1)'))->spin(1.00001));
        $this->assertEquals('cmyk(1.0E-5,0,0,0)', (string) new Cmyk('cmyk(0.00001,0,0,0)'));
    }

    #[Group('css-syntax')]
    public function testVeryLongInputDoesNotHitTheRegexLimits()
    {
        $this->assertEquals('rgb(1,2,3)', (string) new Rgb('rgb(1,' . str_repeat(' ', 2_000_000) . '2,3)'));
    }

    #[DataProvider('invalidCss')]
    #[Group('css-syntax')]
    public function testInvalidCssMustThrow(string $class, string $css)
    {
        $this->expectException(InvalidColorException::class);
        new $class($css);
    }

    public static function invalidCss(): array
    {
        return [
            'rgb cannot keep an alpha' => [Rgb::class, 'rgb(1 2 3 / 0.5)'],
            'rgb out of range' => [Rgb::class, 'rgb(256,0,0)'],
            'rgb negative' => [Rgb::class, 'rgb(-1,0,0)'],
            'rgb rounds above 255' => [Rgb::class, 'rgb(255.6,0,0)'],
            'rgb more than one slash' => [Rgba::class, 'rgb(1/2/3)'],
            'negative saturation' => [Hsl::class, 'hsl(10,-5%,50%)'],
            'negative alpha' => [Rgba::class, 'rgba(1,2,3,-0.5)'],
            'rgb too few values' => [Rgb::class, 'rgb(1,2)'],
            'rgb broken parentheses' => [Rgb::class, '(((10,20,30'],
            'rgba alpha above 100%' => [Rgba::class, 'rgb(1 2 3 / 101%)'],
            'rgba empty alpha' => [Rgba::class, '1,2,3,'],
            'hsl unknown unit' => [Hsl::class, 'hsl(120px 50% 50%)'],
            'hsl percent inside number' => [Hsl::class, 'hsl(120,1%00,50)'],
            'hex with two hashes' => [Hex::class, '##fff'],
            'hex with hashes inside' => [Hex::class, 'f#f#f#'],
            'hex cannot keep an alpha' => [Hex::class, '#f0f8'],
            'hexa with hashes inside' => [Hexa::class, 'ff#ff#ff#80'],
            'oklch alpha above 1' => [Oklch::class, 'oklch(50% 0.1 20 / 2)'],
        ];
    }
}
