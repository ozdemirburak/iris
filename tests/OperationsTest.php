<?php

namespace OzdemirBurak\Iris\Tests;

use OzdemirBurak\Iris\Color\Cmyk;
use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Hsv;
use OzdemirBurak\Iris\Color\Oklch;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Color\Rgba;
use PHPUnit\Framework\Attributes\Group;
use OzdemirBurak\Iris\Color\Hexa;
use PHPUnit\Framework\TestCase;

class OperationsTest extends TestCase
{
    #[Group('operations-saturate')]
    public function testSaturation()
    {
        $this->assertEquals(new Hex('#80ff00'), (new Hsl('90, 80%, 50%'))->saturate(20)->toHex());
        $this->assertEquals(new Hex('#80cc33'), (new Hsl('90, 80%, 50%'))->desaturate(20)->toHex());
        $this->assertEquals(new Hex('#80b34d'), (new Hex('#80cc33'))->desaturate(20));
        $this->assertEquals(new Hex('#808080'), (new Hex('#80cc33'))->grayscale());
    }

    #[Group('operations-lighten')]
    public function testLightness()
    {
        $this->assertEquals(new Hex('#b3f075'), (new Hsl('90, 80%, 50%'))->lighten(20)->toHex());
        $this->assertEquals(new Hex('#4d8a0f'), (new Hsl('90, 80%, 50%'))->darken(20)->toHex());
        $this->assertEquals(new Hex('#4d7a1f'), (new Hex('#80cc33'))->darken(20));
        $this->assertEquals(new Hex('#b3ff66'), (new Hex('#80cc33'))->brighten(20));
    }

    #[Group('operations-spin')]
    public function testSpin()
    {
        $this->assertEquals(new Hex('#f2a60d'), (new Hsl('10,90%,50'))->spin(30)->toHex());
        $this->assertEquals(new Hex('#f20d59'), (new Hsl('10,90%,50'))->spin(-30)->toHex());
    }

    #[Group('operations-mix')]
    public function testMix()
    {
        $this->assertEquals(new Hex('#808080'), (new Hex('#000'))->mix(new Hex('#fff')));
        $this->assertEquals(new Hex('#ff8000'), (new Hex('#ff0000'))->mix(new Hex('#ffff00')));
    }

    #[Group('operations-is-light')]
    public function testIsLight()
    {
        $this->assertFalse((new Hex('#000000'))->isLight());
        $this->assertTrue((new Hex('#ffffff'))->isLight());
        $this->assertTrue((new Hex('#808080'))->isLight());
        $this->assertTrue((new Hex('#888888'))->isLight());
        $this->assertFalse((new Hex('#777777'))->isLight());
        $this->assertFalse((new Hex('#ff0000'))->isLight());
        $this->assertTrue((new Hex('#ffff00'))->isLight());
    }

    #[Group('operations-is-dark')]
    public function testIsDark()
    {
        $this->assertTrue((new Hex('#000000'))->isDark());
        $this->assertFalse((new Hex('#ffffff'))->isDark());
    }

    #[Group('operations-tint')]
    public function testTint()
    {
        $this->assertEquals(new Hex('#80bfff'), (new Hex('#007fff'))->tint(50));
        $this->assertEquals(new Hex('#80bfff'), (new Hex('#007fff'))->tint());
    }

    #[Group('operations-shade')]
    public function testShade()
    {
        $this->assertEquals(new Hex('#004080'), (new Hex('#007fff'))->shade(50));
        $this->assertEquals(new Hex('#004080'), (new Hex('#007fff'))->shade());
    }

    #[Group('operations-fade')]
    public function testFade()
    {
        $this->assertEquals(new Hsla('90,90,50,0.1'), (new Hsl('90,90,50'))->fade(10));
        $this->assertEquals(new Rgba('128,242,13,0.1'), (new Rgb('128,242,13'))->fade(10));
    }

    #[Group('operations-fadeIn')]
    public function testFadeIn()
    {
        $this->assertEquals(new Hsla('90,90,50,0.4'), (new Hsla('90,90,50,0.3'))->fadeIn(10));
        $this->assertEquals(new Rgba('128,242,13,0.4'), (new Rgba('128,242,13,0.3'))->fadeIn(10));
    }

    #[Group('operations-fadeOut')]
    public function testFadeOut()
    {
        $this->assertEquals('hsla(90,90%,50%,0.2)', (string) (new Hsla('90,90,50,0.3'))->fadeOut(10));
        $this->assertEquals('rgba(128,242,13,0.2)', (string) (new Rgba('128,242,13,0.3'))->fadeOut(10));
    }

    #[Group('operations-gradient')]
    public function testGradientTwoColors()
    {
        $gradient = (new Hex('#000000'))->gradient(new Hex('#ffffff'), 5);

        $this->assertCount(5, $gradient);
        $this->assertEquals('#000000', (string) $gradient[0]);
        $this->assertEquals('#404040', (string) $gradient[1]);
        $this->assertEquals('#808080', (string) $gradient[2]);
        $this->assertEquals('#bfbfbf', (string) $gradient[3]);
        $this->assertEquals('#ffffff', (string) $gradient[4]);
    }

    #[Group('operations-gradient')]
    public function testGradientMultipleColors()
    {
        $gradient = (new Hex('#ff0000'))->gradient([new Hex('#00ff00'), new Hex('#0000ff')], 5);

        $this->assertCount(5, $gradient);
        $this->assertEquals('#ff0000', (string) $gradient[0]);
        $this->assertEquals('#808000', (string) $gradient[1]);
        $this->assertEquals('#00ff00', (string) $gradient[2]);
        $this->assertEquals('#008080', (string) $gradient[3]);
        $this->assertEquals('#0000ff', (string) $gradient[4]);
    }

    #[Group('operations-gradient')]
    public function testGradientPreservesColorType()
    {
        $gradient = (new Rgb('255,0,0'))->gradient(new Rgb('0,0,255'), 3);

        $this->assertCount(3, $gradient);
        $this->assertInstanceOf(Rgb::class, $gradient[0]);
        $this->assertInstanceOf(Rgb::class, $gradient[1]);
        $this->assertInstanceOf(Rgb::class, $gradient[2]);
        $this->assertEquals('rgb(255,0,0)', (string) $gradient[0]);
        $this->assertEquals('rgb(128,0,128)', (string) $gradient[1]);
        $this->assertEquals('rgb(0,0,255)', (string) $gradient[2]);
    }

    #[Group('operations-gradient')]
    public function testGradientMinimumSteps()
    {
        $gradient = (new Hex('#000000'))->gradient(new Hex('#ffffff'), 1);
        $this->assertCount(1, $gradient);
        $this->assertEquals('#000000', (string) $gradient[0]);
    }

    #[Group('operations-gradient')]
    public function testGradientTwoSteps()
    {
        $gradient = (new Hex('#000000'))->gradient(new Hex('#ffffff'), 2);
        $this->assertCount(2, $gradient);
        $this->assertEquals('#000000', (string) $gradient[0]);
        $this->assertEquals('#ffffff', (string) $gradient[1]);
    }

    #[Group('operations-gradient')]
    public function testGradientSameColor()
    {
        $gradient = (new Hex('#ff0000'))->gradient(new Hex('#ff0000'), 5);
        $this->assertCount(5, $gradient);
        foreach ($gradient as $color) {
            $this->assertEquals('#ff0000', (string) $color);
        }
    }

    #[Group('operations-gradient')]
    public function testGradientWithHsl()
    {
        $gradient = (new Hsl('0,100%,50%'))->gradient(new Hsl('240,100%,50%'), 3);
        $this->assertCount(3, $gradient);
        $this->assertInstanceOf(Hsl::class, $gradient[0]);
        $this->assertInstanceOf(Hsl::class, $gradient[1]);
        $this->assertInstanceOf(Hsl::class, $gradient[2]);
    }

    #[Group('operations-gradient')]
    public function testGradientWithHsv()
    {
        $gradient = (new Hsv('0,100%,100%'))->gradient(new Hsv('240,100%,100%'), 3);
        $this->assertCount(3, $gradient);
        $this->assertInstanceOf(Hsv::class, $gradient[0]);
        $this->assertInstanceOf(Hsv::class, $gradient[1]);
        $this->assertInstanceOf(Hsv::class, $gradient[2]);
    }

    #[Group('operations-gradient')]
    public function testGradientWithCmyk()
    {
        $gradient = (new Cmyk('0,100,100,0'))->gradient(new Cmyk('100,0,0,0'), 3);
        $this->assertCount(3, $gradient);
        $this->assertInstanceOf(Cmyk::class, $gradient[0]);
        $this->assertInstanceOf(Cmyk::class, $gradient[1]);
        $this->assertInstanceOf(Cmyk::class, $gradient[2]);
    }

    #[Group('operations-gradient')]
    public function testGradientWithOklch()
    {
        $gradient = (new Oklch('0.63 0.26 29'))->gradient(new Oklch('0.45 0.31 264'), 3);
        $this->assertCount(3, $gradient);
        $this->assertInstanceOf(Oklch::class, $gradient[0]);
        $this->assertInstanceOf(Oklch::class, $gradient[1]);
        $this->assertInstanceOf(Oklch::class, $gradient[2]);
    }

    #[Group('operations-gradient')]
    public function testGradientWithRgba()
    {
        $gradient = (new Rgba('255,0,0,0.5'))->gradient(new Rgba('0,0,255,1.0'), 3);
        $this->assertCount(3, $gradient);
        $this->assertInstanceOf(Rgba::class, $gradient[0]);
        $this->assertInstanceOf(Rgba::class, $gradient[1]);
        $this->assertInstanceOf(Rgba::class, $gradient[2]);
    }

    #[Group('operations-gradient')]
    public function testGradientWithHsla()
    {
        $gradient = (new Hsla('0,100%,50%,0.5'))->gradient(new Hsla('240,100%,50%,1.0'), 3);
        $this->assertCount(3, $gradient);
        $this->assertInstanceOf(Hsla::class, $gradient[0]);
        $this->assertInstanceOf(Hsla::class, $gradient[1]);
        $this->assertInstanceOf(Hsla::class, $gradient[2]);
    }

    #[Group('operations-gradient')]
    public function testGradientFourColors()
    {
        $gradient = (new Hex('#ff0000'))->gradient([
            new Hex('#ffff00'),
            new Hex('#00ff00'),
            new Hex('#0000ff')
        ], 7);

        $this->assertCount(7, $gradient);
        $this->assertEquals('#ff0000', (string) $gradient[0]);
        $this->assertEquals('#0000ff', (string) $gradient[6]);
    }

    #[Group('operations-gradient')]
    public function testGradientDefaultSteps()
    {
        $gradient = (new Hex('#000000'))->gradient(new Hex('#ffffff'));
        $this->assertCount(10, $gradient);
    }

    #[Group('operations-gradient')]
    public function testGradientDoesNotMutateOriginal()
    {
        $start = new Hex('#ff0000');
        $end = new Hex('#0000ff');

        $gradient = $start->gradient($end, 5);

        $this->assertEquals('#ff0000', (string) $start);
        $this->assertEquals('#0000ff', (string) $end);
    }

    #[Group('operations-gradient')]
    public function testGradientLargeSteps()
    {
        $gradient = (new Hex('#000000'))->gradient(new Hex('#ffffff'), 100);
        $this->assertCount(100, $gradient);
        $this->assertEquals('#000000', (string) $gradient[0]);
        $this->assertEquals('#ffffff', (string) $gradient[99]);
    }

    #[Group('operations-spin')]
    public function testSpinKeepsFractionalHue()
    {
        $this->assertEquals('hsl(110.5,50%,50%)', (string) (new Hsl('100,50%,50%'))->spin(10.5));
        $this->assertEquals('hsl(100,50%,50%)', (string) (new Hsl('100,50%,50%'))->spin(-360));
        $this->assertEquals('hsl(0,50%,50%)', (string) (new Hsl('10,50%,50%'))->spin(-370));
        $this->assertEquals('hsl(20,50%,50%)', (string) (new Hsl('10,50%,50%'))->spin(370));
        // Rgb::toHsl gives a fractional hue (209.1) for this color
        $this->assertEquals('cmyk(67.3,66,0,40)', (string) (new Cmyk('67,33,0,40'))->spin(30));
    }

    #[Group('operations-mix')]
    public function testMixClampsPercent()
    {
        $this->assertEquals('#8000ff', (string) (new Hex('#000'))->mix(new Hex('#8000ff'), 150));
        $this->assertEquals('#000000', (string) (new Hex('#000'))->mix(new Hex('#8000ff'), -50));
        $this->assertEquals('#ffffff', (string) (new Hex('#000'))->tint(150));
    }

    #[Group('operations-alpha')]
    public function testOperationsKeepAlpha()
    {
        $this->assertEquals('rgba(255,0,0,0.5)', (string) (new Rgba('255,0,0,0.5'))->spin(0));
        $this->assertEquals('rgba(102,153,204,0.3)', (string) (new Rgba('51,102,153,0.3'))->lighten(20));
        $this->assertEquals('rgba(77,128,179,0.3)', (string) (new Rgba('51,102,153,0.3'))->brighten(10));
        $this->assertEquals('hsla(150,100%,50%,0.3)', (string) (new Hsla('150,100,50,0.3'))->lighten(0));
        $this->assertEquals('hsla(150,80%,50%,0.3)', (string) (new Hsla('150,100,50,0.3'))->desaturate(20));
        $this->assertEquals('hsla(210,0%,30%,0.5)', (string) (new Hsla('hsla(200,40%,30%,0.5)'))->saturation(0.00001)->spin(10)->saturation(0));
        $this->assertEquals('#ff000080', (string) (new Hexa('#ff000080'))->lighten(0));
        $this->assertEquals('#00ff804d', (string) (new Hexa('#00ff804d'))->spin(0));
    }

    #[Group('operations-alpha')]
    public function testMixWeightsByAlphaLikeLessAndSass()
    {
        // The more opaque color counts more: 75% blue, and the alpha is the average
        $this->assertEquals('rgba(64,0,191,0.75)', (string) (new Rgba('255,0,0,0.5'))->mix(new Rgba('0,0,255,1.0')));
        $this->assertEquals('rgba(191,191,191,0.75)', (string) (new Rgba('0,0,0,0.5'))->tint(50));
        // A fully transparent color does not change the channels
        $this->assertEquals('rgba(255,0,0,0.5)', (string) (new Rgba('255,0,0,1'))->mix(new Rgba('0,0,255,0'), 50));
        // rgba(255,0,0,0.5) blended onto white: 127.5 is rounded to 128
        $this->assertEquals('#ff8080', (string) (new Hex('#f00'))->mix(new Rgba('0,0,255,0'), 50));
        $this->assertEquals('#808080', (string) (new Hex('#000'))->mix(new Rgb('255,255,255')));
    }

    #[Group('operations-immutability')]
    public function testOperationsDoNotMutateTheColor()
    {
        $hsl = new Hsl('0,0,20');
        $this->assertEquals('hsl(0,0%,40%)', (string) $hsl->lighten(20));
        $this->assertEquals('hsl(0,0%,0%)', (string) $hsl->darken(20));
        $hsl->saturate(10);
        $hsl->spin(90);
        $this->assertEquals('hsl(0,0%,20%)', (string) $hsl);

        $rgb = new Rgb('10,20,30');
        $rgb->tint(50);
        $rgb->shade(50);
        $rgb->brighten(10);
        $rgb->mix(new Rgb('255,255,255'));
        $this->assertEquals('rgb(10,20,30)', (string) $rgb);

        $rgba = new Rgba('1,2,3,0.3');
        $rgba->fade(90);
        $rgba->fadeIn(10);
        $this->assertEquals('rgba(1,2,3,0.3)', (string) $rgba);

        $hsla = new Hsla('1,2,3,0.3');
        $hsla->fadeOut(10);
        $this->assertEquals('hsla(1,2%,3%,0.3)', (string) $hsla);
    }

    #[Group('operations-fade')]
    public function testFadeKeepsHslaModel()
    {
        $faded = (new Hsla('90,90,50,0.3'))->fade(50);
        $this->assertInstanceOf(Hsla::class, $faded);
        $this->assertEquals('hsla(90,90%,50%,0.5)', (string) $faded);
        // A very small alpha must not be printed in scientific notation and then fail to parse
        $this->assertEquals('hsla(210,50%,7.8%,0)', (string) (new Rgb('10,20,30'))->fade(0.005)->toHsla());
    }

    #[Group('operations-gradient')]
    public function testGradientWithAlphaValues()
    {
        $gradient = (new Rgba('255,0,0,0.5'))->gradient(new Rgba('0,0,255,1.0'), 3);
        $this->assertEquals(['rgba(255,0,0,0.5)', 'rgba(64,0,191,0.75)', 'rgba(0,0,255,1)'], array_map('strval', $gradient));
    }

    #[Group('operations-gradient')]
    public function testGradientStopsKeepTheirOwnAlpha()
    {
        $gradient = (new Rgba('rgba(255,0,0,0.2)'))->gradient([new Hex('#00ff00'), new Hex('#0000ff')], 5);
        $this->assertEquals(
            ['rgba(255,0,0,0.2)', 'rgba(26,230,0,0.6)', 'rgba(0,255,0,1)', 'rgba(0,128,128,1)', 'rgba(0,0,255,1)'],
            array_map('strval', $gradient)
        );
        $this->assertEquals('rgba(0,0,255,1)', (string) (new Rgba('rgba(255,0,0,0.2)'))->gradient([new Hex('#0f0'), new Hex('#00f')], 2)[1]);
        $this->assertEquals('#0000ffff', (string) (new Hexa('#ff000033'))->gradient(new Hex('#00f'), 3)[2]);
    }

    #[Group('operations-gradient')]
    public function testGradientStartsAndEndsAtTheExactColors()
    {
        $gradient = (new Hsv('hsv(200,30%,70%)'))->gradient(new Hsv('hsv(10,40%,20%)'), 3);
        $this->assertEquals('hsv(200,30%,70%)', (string) $gradient[0]);
        $this->assertEquals('hsv(10,40%,20%)', (string) $gradient[2]);
    }

    #[Group('operations-fade')]
    public function testFadeKeepsTheHexFamily()
    {
        $this->assertEquals('#ff000080', (string) (new Hex('#f00'))->fade(50));
        $this->assertInstanceOf(Hexa::class, (new Hexa('#ff000055'))->fadeIn(10));
        // fadeIn() uses the exact alpha, 0x55 / 255 = 0.3333, and not the rounded 0.33
        $this->assertEquals('#ff000055', (string) (new Hexa('#ff000055'))->fadeIn(0));
    }

    #[Group('operations-alpha')]
    public function testOperationsKeepTheRgbaBackground()
    {
        $rgba = (new Rgba('rgba(255,255,255,0.5)'))->background(new Rgb('0,0,0'));
        $this->assertEquals('rgb(128,128,128)', (string) $rgba->lighten(0)->toRgb());
        $this->assertEquals('rgb(0,0,0)', (string) $rgba->background());
        $clone = $rgba->clone();
        $clone->background()->red(255);
        $this->assertEquals('rgb(0,0,0)', (string) $rgba->background(), 'A clone must not share the background');
    }

    #[Group('operations-gradient')]
    public function testGradientEdgeCases()
    {
        $this->assertEquals(['#ff0000'], array_map('strval', (new Hex('#f00'))->gradient([], 5)));
        $this->assertEquals(
            ['#ff0000', '#00ff00'],
            array_map('strval', (new Hex('#f00'))->gradient([new Hex('#ff0'), new Hex('#0f0')], 2))
        );
        $this->assertEquals(
            ['#ff0000', '#00ff00', '#0000ff'],
            array_map('strval', (new Hex('#f00'))->gradient([new Hex('#ff0'), new Hex('#0f0'), new Hex('#00f')], 3))
        );
    }

    #[Group('operations-gradient')]
    public function testGradientWithUnevenSegments()
    {
        // 3 colors and 4 steps: the first segment gets the extra step
        $gradient = (new Hex('#f00'))->gradient([new Hex('#0f0'), new Hex('#00f')], 4);
        $this->assertEquals(['#ff0000', '#808000', '#00ff00', '#0000ff'], array_map('strval', $gradient));
    }

    #[Group('operations-is-light')]
    public function testIsLightThreshold()
    {
        // The darkness of #808080 is 0.498 and of #7d7d7d is 0.510; the threshold is 0.5
        $this->assertTrue((new Hex('#808080'))->isLight());
        $this->assertFalse((new Hex('#7d7d7d'))->isLight());
    }
}
