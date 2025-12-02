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
        $this->assertEquals([70.0, 0.15, 150.0], $oklch->values());
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
}
