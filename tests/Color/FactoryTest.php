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
}
