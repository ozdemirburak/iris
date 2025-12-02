<?php

namespace OzdemirBurak\Iris\Tests\Color;

use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hexa;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Rgba;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AlphaConversionTest extends TestCase
{
    #[DataProvider('rgbaToHexaProvider')]
    public function testRgbaToHexaPreservesOriginalRgbValues(string $rgba, string $expectedHexa): void
    {
        $color = new Rgba($rgba);
        $this->assertEquals($expectedHexa, (string) $color->toHexa());
    }

    public static function rgbaToHexaProvider(): array
    {
        return [
            'red with 50% alpha' => ['rgba(255,0,0,0.5)', '#ff000080'],
            'green with 50% alpha' => ['rgba(0,255,0,0.5)', '#00ff0080'],
            'blue with 50% alpha' => ['rgba(0,0,255,0.5)', '#0000ff80'],
            'gray with 50% alpha' => ['rgba(102,102,102,0.5)', '#66666680'],
            'white with 20% alpha' => ['rgba(255,255,255,0.2)', '#ffffff33'],
            'black with 80% alpha' => ['rgba(0,0,0,0.8)', '#000000cc'],
            'full opacity' => ['rgba(255,128,64,1.0)', '#ff8040ff'],
            'zero opacity' => ['rgba(255,128,64,0)', '#ff804000'],
            'low rgb values with alpha' => ['rgba(11,22,33,0.2)', '#0b162133'],
        ];
    }

    #[DataProvider('hslaToHexaProvider')]
    public function testHslaToHexaPreservesOriginalHslValues(string $hsla, string $expectedHexa): void
    {
        $color = new Hsla($hsla);
        $this->assertEquals($expectedHexa, (string) $color->toHexa());
    }

    public static function hslaToHexaProvider(): array
    {
        return [
            'green with 30% alpha' => ['hsla(150,100%,50%,0.3)', '#00ff804d'],
            'red with 50% alpha' => ['hsla(0,100%,50%,0.5)', '#ff000080'],
            'blue with 70% alpha' => ['hsla(240,100%,50%,0.7)', '#0000ffb3'],
            'full opacity' => ['hsla(300,100%,50%,1.0)', '#ff00ffff'],
            'zero opacity' => ['hsla(60,100%,50%,0)', '#ffff0000'],
        ];
    }

    #[DataProvider('hexaRoundTripProvider')]
    public function testHexaRoundTripConversion(string $hexa): void
    {
        $color = new Hexa($hexa);
        $this->assertEquals(strtolower($hexa), strtolower((string) $color->toHexa()));
    }

    public static function hexaRoundTripProvider(): array
    {
        return [
            ['#ff000080'],
            ['#00ff00cc'],
            ['#0000ff33'],
            ['#ffffff00'],
            ['#000000ff'],
            ['#d946ef88'],
        ];
    }

    #[DataProvider('alphaHexConversionProvider')]
    public function testAlphaFloatToHexConversion(float $alpha, string $expectedHex): void
    {
        $rgba = new Rgba("rgba(255,0,0,{$alpha})");
        $hexa = (string) $rgba->toHexa();
        $actualHex = substr($hexa, -2);
        $this->assertEquals($expectedHex, $actualHex, "Alpha {$alpha} should convert to hex {$expectedHex}");
    }

    public static function alphaHexConversionProvider(): array
    {
        return [
            'alpha 0' => [0.0, '00'],
            'alpha 0.1' => [0.1, '1a'],
            'alpha 0.2' => [0.2, '33'],
            'alpha 0.25' => [0.25, '40'],
            'alpha 0.3' => [0.3, '4d'],
            'alpha 0.5' => [0.5, '80'],
            'alpha 0.7' => [0.7, 'b3'],
            'alpha 0.75' => [0.75, 'bf'],
            'alpha 0.8' => [0.8, 'cc'],
            'alpha 0.9' => [0.9, 'e6'],
            'alpha 1.0' => [1.0, 'ff'],
        ];
    }

    public function testHexaToRgbaPreservesAlpha(): void
    {
        $hexa = new Hexa('#ff000080');
        $rgba = $hexa->toRgba();

        $this->assertEquals(255, $rgba->red());
        $this->assertEquals(0, $rgba->green());
        $this->assertEquals(0, $rgba->blue());
        $this->assertEqualsWithDelta(0.5, $rgba->alpha(), 0.01);
    }

    public function testHexaToHslaPreservesAlpha(): void
    {
        $hexa = new Hexa('#ff000080');
        $hsla = $hexa->toHsla();

        $this->assertEquals(0, $hsla->hue());
        $this->assertEquals(100, $hsla->saturation());
        $this->assertEquals(50, $hsla->lightness());
        $this->assertEqualsWithDelta(0.5, $hsla->alpha(), 0.01);
    }

    public function testRgbaToHslaPreservesAlpha(): void
    {
        $rgba = new Rgba('rgba(255,0,0,0.5)');
        $hsla = $rgba->toHsla();

        $this->assertEquals(0, (int) $hsla->hue());
        $this->assertEquals(100, (int) $hsla->saturation());
        $this->assertEquals(50, (int) $hsla->lightness());
        $this->assertEquals(0.5, $hsla->alpha());
    }

    public function testHslaToRgbaPreservesAlpha(): void
    {
        $hsla = new Hsla('hsla(0,100%,50%,0.5)');
        $rgba = $hsla->toRgba();

        $this->assertEquals(255, $rgba->red());
        $this->assertEquals(0, $rgba->green());
        $this->assertEquals(0, $rgba->blue());
        $this->assertEquals(0.5, $rgba->alpha());
    }

    public function testIssue48RgbaToHexa(): void
    {
        $rgba = new Rgba('rgba(102, 102, 102, 0.5)');

        $this->assertEquals('#66666680', (string) $rgba->toHexa());
        $this->assertEquals('hsla(0,0%,40%,0.5)', (string) $rgba->toHsla());
    }

    public function testIssue48HslaToHexa(): void
    {
        $hsla = new Hsla('hsla(150,100%,50%,0.7)');

        $this->assertEquals('#00ff80b3', (string) $hsla->toHexa());
    }

    public function testIssue48HexaConversions(): void
    {
        $hexa = new Hexa('#d946ef88');

        $this->assertEqualsWithDelta(0.53, $hexa->alpha(), 0.01);
        $this->assertEquals('#d946ef', (string) $hexa->toHex());
    }

    public function testIssue37RgbaToHexaWithAlpha(): void
    {
        $rgba = new Rgba('rgba(102, 102, 102, 0.5)');

        $hexa = $rgba->toHexa();
        $this->assertEquals('66', $hexa->red());
        $this->assertEquals('66', $hexa->green());
        $this->assertEquals('66', $hexa->blue());
        $this->assertEquals(0.5, $hexa->alpha());
    }

    /**
     * Test for issue #37: rgba()->toHexa and hsla()->toHexa not working as expected
     * @see https://github.com/ozdemirburak/iris/issues/37
     *
     * The bug was that toHexa() was returning alpha-composited values (#b2b2b27f)
     * instead of preserving original RGB values (#66666680)
     */
    public function testIssue37FullScenario(): void
    {
        $rgba = new Rgba('rgba(102, 102, 102, 0.5)');

        // Original RGBA
        $this->assertEquals('rgba(102,102,102,0.5)', (string) $rgba);

        // toRgb applies alpha compositing against white background
        // (1-0.5)*255 + 0.5*102 = 127.5 + 51 = 178.5 ≈ 178
        $this->assertEquals('rgb(178,178,178)', (string) $rgba->toRgb());

        // toHex also applies alpha compositing
        $this->assertEquals('#b2b2b2', (string) $rgba->toHex());

        // toHexa MUST preserve original RGB values with alpha
        // This was the bug: it returned #b2b2b27f instead of #66666680
        $this->assertEquals('#66666680', (string) $rgba->toHexa());

        // toHsl applies alpha compositing
        $hsl = $rgba->toHsl();
        $this->assertEquals(0, (int) $hsl->hue());
        $this->assertEquals(0, (int) $hsl->saturation());
        $this->assertEqualsWithDelta(69.8, (float) $hsl->lightness(), 0.5);

        // toHsla preserves original color with alpha
        $this->assertEquals('hsla(0,0%,40%,0.5)', (string) $rgba->toHsla());
    }

    /**
     * Additional test for issue #37: HSLA to HEXA conversion
     */
    public function testIssue37HslaToHexa(): void
    {
        // Gray color in HSLA format with 50% alpha
        $hsla = new Hsla('hsla(0,0%,40%,0.5)');

        // toHexa should preserve the original color, not apply alpha compositing
        // 40% lightness gray = rgb(102,102,102) = #666666
        $hexa = $hsla->toHexa();

        $this->assertEquals('66', $hexa->red());
        $this->assertEquals('66', $hexa->green());
        $this->assertEquals('66', $hexa->blue());
        $this->assertEquals(0.5, $hexa->alpha());
        $this->assertEquals('#66666680', (string) $hexa);
    }

    public function testHslaWithOpacityConversions(): void
    {
        $color = new Hsla('hsla(150,100%,50%,0.7)');

        // toHex applies alpha compositing against white
        $this->assertEquals('#4cffa6', (string) $color->toHex());

        // toHexa preserves original HSL color with alpha
        $this->assertEquals('#00ff80b3', (string) $color->toHexa());

        // toHsl applies alpha compositing, changing lightness
        $hsl = $color->toHsl();
        $this->assertEquals(150, (int) round((float) $hsl->hue()));
        $this->assertEquals(100, (int) $hsl->saturation());

        // toHsla preserves original values
        $this->assertEquals('hsla(150,100%,50%,0.7)', (string) $color->toHsla());

        // toRgb applies alpha compositing against white
        $this->assertEquals('rgb(76,255,166)', (string) $color->toRgb());

        // toRgba preserves original RGB with alpha
        $this->assertEquals('rgba(0,255,128,0.7)', (string) $color->toRgba());
    }

    public function testHexaWithOpacityConversions(): void
    {
        $color = new Hexa('#d946ef88');

        // toHex strips alpha (returns RGB only)
        $this->assertEquals('#d946ef', (string) $color->toHex());

        // toHexa round-trips correctly
        $this->assertEquals('#d946ef88', (string) $color->toHexa());

        // toHsl gives the base color HSL
        $hsl = $color->toHsl();
        $this->assertEqualsWithDelta(292, (float) $hsl->hue(), 1);
        $this->assertEqualsWithDelta(84, (float) $hsl->saturation(), 1);
        $this->assertEqualsWithDelta(61, (float) $hsl->lightness(), 1);

        // toHsla preserves alpha
        $hsla = $color->toHsla();
        $this->assertEqualsWithDelta(0.53, $hsla->alpha(), 0.01);

        // toRgb gives the base RGB values
        $rgb = $color->toRgb();
        $this->assertEquals(217, $rgb->red());
        $this->assertEquals(70, $rgb->green());
        $this->assertEquals(239, $rgb->blue());

        // toRgba preserves alpha
        $rgba = $color->toRgba();
        $this->assertEquals(217, $rgba->red());
        $this->assertEquals(70, $rgba->green());
        $this->assertEquals(239, $rgba->blue());
        $this->assertEqualsWithDelta(0.53, $rgba->alpha(), 0.01);
    }

    public function testAllAlphaConversionsVisuallyConsistent(): void
    {
        // When converting colors with alpha, all alpha-preserving conversions
        // should produce visually identical colors in a browser

        $hsla = new Hsla('hsla(150,100%,50%,0.7)');

        // These should all render as the same visual color (green with 70% opacity)
        $hexa = $hsla->toHexa();
        $rgba = $hsla->toRgba();
        $hslaBack = $hsla->toHsla();

        // Verify RGBA values are the pure green
        $this->assertEquals(0, $rgba->red());
        $this->assertEquals(255, $rgba->green());
        $this->assertEquals(128, $rgba->blue());
        $this->assertEquals(0.7, $rgba->alpha());

        // Verify HEXA has same RGB as RGBA
        $this->assertEquals('00', $hexa->red());
        $this->assertEquals('ff', $hexa->green());
        $this->assertEquals('80', $hexa->blue());
        $this->assertEquals(0.7, $hexa->alpha());

        // Verify HSLA is unchanged
        $this->assertEquals(150, (int) $hslaBack->hue());
        $this->assertEquals(100, (int) $hslaBack->saturation());
        $this->assertEquals(50, (int) $hslaBack->lightness());
        $this->assertEquals(0.7, $hslaBack->alpha());
    }
}
