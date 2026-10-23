<?php

namespace OzdemirBurak\Iris\Tests\Color;

use OzdemirBurak\Iris\Color\Factory;
use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Hsv;
use OzdemirBurak\Iris\Color\Oklch;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Color\Rgba;
use PHPUnit\Framework\Attributes\Group;
use OzdemirBurak\Iris\Color\Cmyk;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    #[Group('factory-init')]
    public function testHexReturnType()
    {
        foreach (['#fff', '#ffffff', 'fff', 'ffffff'] as $c) {
            $color = Factory::init($c);
            $this->assertInstanceOf(Hex::class, $color);
        }
    }

    #[Group('factory-init')]
    public function testNamedReturnType()
    {
        foreach (['fuchsia', 'olive', 'blue'] as $c) {
            $color = Factory::init($c);
            $this->assertInstanceOf(Hex::class, $color);
        }
    }

    #[Group('factory-init')]
    public function testRgbReturnType()
    {
        foreach (['rgb(255, 2, 56)', '30, 53,122'] as $c) {
            $color = Factory::init($c);
            $this->assertInstanceOf(Rgb::class, $color);
        }
    }

    #[Group('factory-init')]
    public function testRgbaReturnType()
    {
        foreach (['rgba(255, 2, 56, 0.5)', '30, 53,122, 1', '112,112,122,0.3', '66,66,66,0.333'] as $c) {
            $color = Factory::init($c);
            $this->assertInstanceOf(Rgba::class, $color);
        }
    }

    #[Group('factory-init')]
    public function testHslReturnType()
    {
        $color = Factory::init('hsl(200, 100%, 50%)');
        $this->assertInstanceOf(Hsl::class, $color);
    }

    #[Group('factory-init')]
    public function testHslaReturnType()
    {
        foreach (['hsla(200, 100%, 50%, 0.5)', '200, 100%, 50%, 0.5'] as $c) {
            $color = Factory::init($c);
            $this->assertInstanceOf(Hsla::class, $color);
        }
    }

    #[Group('factory-init')]
    public function testHsvReturnType()
    {
        $color = Factory::init('hsv(200, 100%, 50%)');
        $this->assertInstanceOf(Hsv::class, $color);
    }

    #[Group('factory-init')]
    public function testOklchReturnType()
    {
        $color = Factory::init('oklch(70%, 0.15, 150)');
        $this->assertInstanceOf(Oklch::class, $color);
    }

    #[Group('factory-init')]
    public function testAmbiguousString()
    {
        $this->expectException(\OzdemirBurak\Iris\Exceptions\AmbiguousColorString::class);
        Factory::init('100, 20%, 5%');
    }

    public function testSelfConversion()
    {
        $this->assertEquals(Factory::init("hsv(0, 36%, 72%)")->toHsl()->toHsv(), new Hsv("hsv(0, 36%, 72%)"));
        $this->assertEquals(Factory::init("hsl(0, 33%, 66%)")->toHsv()->toHsl(), new Hsl("hsl(0, 33%, 66%)"));
    }

    #[Group('factory-init')]
    public function testOklchSpaceSeparatedSyntax()
    {
        $color = Factory::init('oklch(62.8% 0.2577 29.23)');
        $this->assertInstanceOf(Oklch::class, $color);
        $this->assertEquals('#ff0000', (string) $color->toHex());
        // The string form of an Oklch color can be read back
        $this->assertEquals('oklch(62.8% 0.2577 29.23)', (string) Factory::init((string) (new Hex('#ff0000'))->toOklch()));
    }

    #[Group('factory-init')]
    public function testSurroundingWhitespace()
    {
        $this->assertEquals('#ffffff', (string) Factory::init("#fff\n"));
        $this->assertEquals('rgb(1,2,3)', (string) Factory::init(' rgb( 1, 2, 3 ) '));
    }

    #[Group('factory-init')]
    public function testCmykOrRgbaGuess()
    {
        $this->assertInstanceOf(Cmyk::class, Factory::init('0,0,0,5'));
        $this->assertInstanceOf(Cmyk::class, Factory::init('10,20,30,40'));
        $this->assertInstanceOf(Cmyk::class, Factory::init('10,20,30,100'));
        $this->assertInstanceOf(Rgba::class, Factory::init('10,20,30,0'));
        $this->assertInstanceOf(Rgba::class, Factory::init('255,0,0,1'));
        $this->assertInstanceOf(Rgba::class, Factory::init('255,0,0,0.5'));
    }

    #[Group('factory-init')]
    public function testExceptionsAreInvalidArguments()
    {
        try {
            Factory::init('100, 20%, 5%');
            $this->fail('An ambiguous string must throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertInstanceOf(\OzdemirBurak\Iris\Exceptions\AmbiguousColorString::class, $e);
        }
        $this->expectException(\InvalidArgumentException::class);
        new Hex('not a color');
    }

    #[Group('factory-init')]
    public function testInvalidInput()
    {
        foreach (['', " \f ", 'hwb(0 0% 0%)', 'color(srgb 1 0 0)', 'cafe'] as $input) {
            try {
                Factory::init($input);
                $this->fail("'$input' must be rejected");
            } catch (\OzdemirBurak\Iris\Exceptions\InvalidColorException $e) {
                $this->assertNotEmpty($e->getMessage());
            }
        }
        // A 4-digit hex needs a "#", an 8-digit hex does not
        $this->assertInstanceOf(\OzdemirBurak\Iris\Color\Hexa::class, Factory::init('#cafe'));
        $this->assertInstanceOf(\OzdemirBurak\Iris\Color\Hexa::class, Factory::init('cafebabe'));
    }
}
