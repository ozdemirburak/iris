<?php

namespace OzdemirBurak\Iris\Tests\Color;

use OzdemirBurak\Iris\Color\Cmyk;
use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hexa;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Hsv;
use OzdemirBurak\Iris\Color\Oklch;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Color\Rgba;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class OklchTest extends TestCase
{
    #[Group('oklch-construction')]
    public function testDigitString(): void
    {
        $oklch = new Oklch('oklch(70%, 0.15, 150)');
        $this->assertEquals(70, $oklch->lightness());
        $this->assertEquals(0.15, $oklch->chroma());
        $this->assertEquals(150, $oklch->hue());
        $this->assertEquals([70.0, 0.15, 150.0, 1.0], $oklch->values());
    }

    #[Group('oklch-construction')]
    public function testSpaceSeparatedSyntax(): void
    {
        $oklch = new Oklch('oklch(40.1% 0.123 21.57)');
        $this->assertEquals(40.1, $oklch->lightness());
        $this->assertEquals(0.123, $oklch->chroma());
        $this->assertEquals(21.57, $oklch->hue());
    }

    #[Group('oklch-construction')]
    public function testSpaceSeparatedSyntaxWithoutCommas(): void
    {
        $oklch = new Oklch('oklch(59.69% 0.156 49.77)');
        $this->assertEquals(59.69, $oklch->lightness());
        $this->assertEquals(0.156, $oklch->chroma());
        $this->assertEquals(49.77, $oklch->hue());
    }

    #[Group('oklch-construction')]
    public function testSpaceSeparatedWithAlpha(): void
    {
        // The alpha is kept, see testAlpha()
        $oklch = new Oklch('oklch(59.69% 0.156 49.77 / .5)');
        $this->assertEquals(59.69, $oklch->lightness());
        $this->assertEquals(0.156, $oklch->chroma());
        $this->assertEquals(49.77, $oklch->hue());
        $this->assertEquals(0.5, $oklch->alpha());
    }

    #[Group('oklch-construction')]
    public function testSpaceSeparatedWithAlphaNoSpace(): void
    {
        $oklch = new Oklch('oklch(59.69% 0.156 49.77/.5)');
        $this->assertEquals(59.69, $oklch->lightness());
        $this->assertEquals(0.156, $oklch->chroma());
        $this->assertEquals(49.77, $oklch->hue());
        $this->assertEquals(0.5, $oklch->alpha());
    }

    #[Group('oklch-construction')]
    public function testMixedSeparators(): void
    {
        // Mix of spaces and commas should work
        $oklch = new Oklch('oklch(50%, 0.1 180)');
        $this->assertEquals(50, $oklch->lightness());
        $this->assertEquals(0.1, $oklch->chroma());
        $this->assertEquals(180, $oklch->hue());
    }

    #[Group('oklch-construction')]
    public function testInvalidColor(): void
    {
        $this->expectException(\OzdemirBurak\Iris\Exceptions\InvalidColorException::class);
        $this->expectExceptionMessage('Invalid OKLCH value');
        new Oklch('oklch(150%, 0.15, 150)');
    }

    #[Group('oklch-conversion')]
    public function testToRgb(): void
    {
        $oklch = new Oklch('oklch(70%, 0.15, 150)');
        $rgb = $oklch->toRgb();
        $this->assertInstanceOf(Rgb::class, $rgb);
    }

    #[Group('oklch-conversion')]
    public function testToHex(): void
    {
        $oklch = new Oklch('oklch(70%, 0.15, 150)');
        $hex = $oklch->toHex();
        $this->assertInstanceOf(Hex::class, $hex);
    }

    #[Group('oklch-conversion')]
    public function testFromRgb(): void
    {
        // Test red
        $hex = new Hex('#ff0000');
        $oklch = $hex->toOklch();
        $this->assertInstanceOf(Oklch::class, $oklch);
        $this->assertEqualsWithDelta(62.8, $oklch->lightness(), 1);
        $this->assertEqualsWithDelta(0.26, $oklch->chroma(), 0.01);
        $this->assertEqualsWithDelta(29, $oklch->hue(), 1);
    }

    #[Group('oklch-conversion')]
    public function testFromRgbGreen(): void
    {
        $hex = new Hex('#00ff00');
        $oklch = $hex->toOklch();
        $this->assertEqualsWithDelta(86.6, $oklch->lightness(), 1);
        $this->assertEqualsWithDelta(0.29, $oklch->chroma(), 0.01);
        $this->assertEqualsWithDelta(142, $oklch->hue(), 1);
    }

    #[Group('oklch-conversion')]
    public function testFromRgbBlue(): void
    {
        $hex = new Hex('#0000ff');
        $oklch = $hex->toOklch();
        $this->assertEqualsWithDelta(45.2, $oklch->lightness(), 1);
        $this->assertEqualsWithDelta(0.31, $oklch->chroma(), 0.01);
        $this->assertEqualsWithDelta(264, $oklch->hue(), 1);
    }

    #[Group('oklch-conversion')]
    public function testFromRgbWhite(): void
    {
        $hex = new Hex('#ffffff');
        $oklch = $hex->toOklch();
        $this->assertEquals(100, $oklch->lightness());
        $this->assertEqualsWithDelta(0, $oklch->chroma(), 0.001);
    }

    #[Group('oklch-conversion')]
    public function testFromRgbBlack(): void
    {
        $hex = new Hex('#000000');
        $oklch = $hex->toOklch();
        $this->assertEquals(0, $oklch->lightness());
        $this->assertEquals(0, $oklch->chroma());
    }

    #[Group('oklch-conversion')]
    public function testToString(): void
    {
        $oklch = new Oklch('oklch(70%, 0.15, 150)');
        $this->assertEquals('oklch(70% 0.15 150)', (string) $oklch);
    }

    #[Group('oklch-conversion')]
    public function testAllConversions(): void
    {
        $oklch = new Oklch('oklch(70%, 0.15, 150)');

        $this->assertInstanceOf(Rgb::class, $oklch->toRgb());
        $this->assertInstanceOf(Rgba::class, $oklch->toRgba());
        $this->assertInstanceOf(Hex::class, $oklch->toHex());
        $this->assertInstanceOf(Hexa::class, $oklch->toHexa());
        $this->assertInstanceOf(Hsl::class, $oklch->toHsl());
        $this->assertInstanceOf(Hsla::class, $oklch->toHsla());
        $this->assertInstanceOf(Hsv::class, $oklch->toHsv());
        $this->assertInstanceOf(Cmyk::class, $oklch->toCmyk());
        $this->assertInstanceOf(Oklch::class, $oklch->toOklch());
    }

    #[Group('oklch-conversion')]
    public function testRoundTrip(): void
    {
        // Test that RGB -> OKLCH -> RGB produces similar results
        $originalHex = new Hex('#4cb86a');
        $oklch = $originalHex->toOklch();
        $backToHex = $oklch->toHex();

        // Allow for some rounding differences
        $this->assertEqualsWithDelta(
            hexdec(substr((string) $originalHex, 1, 2)),
            hexdec(substr((string) $backToHex, 1, 2)),
            2
        );
    }

    #[Group('oklch-setters')]
    public function testSetters(): void
    {
        $oklch = new Oklch('oklch(50%, 0.1, 180)');

        $oklch->lightness(70);
        $this->assertEquals(70, $oklch->lightness());

        $oklch->chroma(0.2);
        $this->assertEquals(0.2, $oklch->chroma());

        $oklch->hue(90);
        $this->assertEquals(90, $oklch->hue());
    }

    #[Group('oklch-setters')]
    public function testHueWrapping(): void
    {
        $oklch = new Oklch('oklch(50%, 0.1, 180)');

        $oklch->hue(400);
        $this->assertEquals(40, $oklch->hue());

        $oklch->hue(-30);
        $this->assertEquals(330, $oklch->hue());
    }

    #[Group('oklch-construction')]
    public function testUnitlessLightnessIsAFraction(): void
    {
        // CSS Color 4: a lightness without "%" is a number from 0 to 1
        $this->assertEquals('#ffffff', (string) (new Oklch('oklch(1 0 0)'))->toHex());
        $this->assertEquals('#ff0000', (string) (new Oklch('oklch(0.628 0.2577 29.23)'))->toHex());
        $this->assertEquals(62.8, (new Oklch('oklch(0.628 0.2577 29.23)'))->lightness());
        // Values above 1 are still percentages
        $this->assertEquals(70, (new Oklch('oklch(70, 0.15, 150)'))->lightness());
    }

    #[Group('oklch-construction')]
    public function testCssSyntaxVariants(): void
    {
        $this->assertEquals('oklch(70% 0.15 150 / 0.5)', (string) new Oklch('oklch(70% 0.15 150deg / 50%)'));
        $this->assertEquals('oklch(70% 0.15 150 / 0.5)', (string) new Oklch('oklch(70% .15 150 / .5)'));
    }

    #[Group('oklch-construction')]
    public function testMalformedNumbersMustThrow(): void
    {
        foreach (['oklch(1.2.3 0.1 20)', 'oklch(. . .)', 'oklch(50% 0.1)', 'oklch(50% 0.1 20 30)'] as $code) {
            try {
                new Oklch($code);
                $this->fail("$code must be rejected");
            } catch (\OzdemirBurak\Iris\Exceptions\InvalidColorException $e) {
                $this->assertStringContainsString('Invalid OKLCH value', $e->getMessage());
            }
        }
    }

    #[Group('oklch-conversion')]
    public function testAchromaticColorsHaveZeroHue(): void
    {
        $this->assertEquals('oklch(100% 0 0)', (string) (new Hex('#ffffff'))->toOklch());
        $this->assertEquals('oklch(59.99% 0 0)', (string) (new Hex('#808080'))->toOklch());
        $this->assertEquals('oklch(0% 0 0)', (string) (new Hex('#000000'))->toOklch());
    }

    #[Group('oklch-alpha')]
    public function testAlpha(): void
    {
        $oklch = new Oklch('oklch(62.8% 0.2577 29.23 / 0.5)');
        $this->assertEquals(0.5, $oklch->alpha());
        $this->assertEquals([62.8, 0.2577, 29.23, 0.5], $oklch->values());
        $this->assertEquals('oklch(62.8% 0.2577 29.23 / 0.5)', (string) $oklch);
        $this->assertEquals($oklch, new Oklch((string) $oklch), 'The string form must parse again');
        $this->assertEquals('oklch(62.8% 0.2577 29.23)', (string) $oklch->alpha(1), 'An alpha of 1 is not written');
    }

    #[Group('oklch-alpha')]
    public function testAlphaInConversions(): void
    {
        $oklch = new Oklch('oklch(62.8% 0.2577 29.23 / 0.5)');
        // Alpha models keep the color and the alpha
        $this->assertEquals('rgba(255,0,0,0.5)', (string) $oklch->toRgba());
        $this->assertEquals('#ff000080', (string) $oklch->toHexa());
        $this->assertEquals('hsla(0,100%,50%,0.5)', (string) $oklch->toHsla());
        $this->assertEquals('oklch(62.8% 0.2577 29.23 / 0.5)', (string) (new Rgba('255,0,0,0.5'))->toOklch());
        $this->assertEquals('oklch(62.8% 0.2577 29.23 / 0.5)', (string) (new Hexa('#ff000080'))->toOklch());
        $this->assertEquals('oklch(62.8% 0.2577 29.23 / 0.5)', (string) (new Hsla('0,100,50,0.5'))->toOklch());
        // Models without alpha blend onto white, like Rgba and Hsla do
        $this->assertEquals('#ff8080', (string) $oklch->toHex());
        $this->assertEquals((string) (new Rgba('255,0,0,0.5'))->toRgb(), (string) $oklch->toRgb());
    }

    #[Group('oklch-alpha')]
    public function testOperationsKeepAlpha(): void
    {
        $oklch = new Oklch('oklch(62.8% 0.2577 29.23 / 0.5)');
        $this->assertEquals('oklch(62.8% 0.2577 29.23 / 0.5)', (string) $oklch->spin(0));
        $this->assertEquals(0.5, $oklch->lighten(10)->alpha());
        $faded = (new Oklch('oklch(62.8% 0.2577 29.23)'))->fade(30);
        $this->assertInstanceOf(Oklch::class, $faded);
        $this->assertEquals('oklch(62.8% 0.2577 29.23 / 0.3)', (string) $faded);
        $gradient = $oklch->gradient(new Oklch('oklch(45.2% 0.3132 264.05)'), 3);
        $this->assertEquals([0.5, 0.75, 1.0], array_map(fn (Oklch $color) => $color->alpha(), $gradient));
    }

    #[Group('oklch-conversion')]
    public function testConversionValues(): void
    {
        $red = new Oklch('oklch(62.8% 0.2577 29.23)');
        $this->assertEquals('hsl(0,100%,50%)', (string) $red->toHsl());
        $this->assertEquals('hsv(0,100%,100%)', (string) $red->toHsv());
        $this->assertEquals('cmyk(0,100,100,0)', (string) $red->toCmyk());
        $this->assertEquals('oklch(62.8% 0.2577 29.23)', (string) (new Hsv('0,100,100'))->toOklch());
        $this->assertEquals('oklch(62.8% 0.2577 29.23)', (string) (new Cmyk('0,100,100,0'))->toOklch());
    }
}
