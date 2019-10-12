<?php

namespace OzdemirBurak\Iris\Tests\Color;

use OzdemirBurak\Iris\Color\Factory;
use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Hsv;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Color\Rgba;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * @group factory-init
     */
    public function testHexReturnType()
    {
        foreach (['#fff', '#ffffff', 'fff', 'ffffff'] as $c) {
            $color = Factory::init($c);
            $this->assertInstanceOf(Hex::class, $color);
        }
    }

    /**
     * @group factory-init
     */
    public function testNamedReturnType()
    {
        foreach (['fuchsia', 'olive', 'blue'] as $c) {
            $color = Factory::init($c);
            $this->assertInstanceOf(Hex::class, $color);
        }
    }

    /**
     * @group factory-init
     */
    public function testRgbReturnType()
    {
        foreach (['rgb(255, 2, 56)', '30, 53,122'] as $c) {
            $color = Factory::init($c);
            $this->assertInstanceOf(Rgb::class, $color);
        }
    }

    /**
     * @group factory-init
     */
    public function testRgbaReturnType()
    {
        foreach (['rgba(255, 2, 56, 0.5)', '30, 53,122, 1', '112,112,122,0.3'] as $c) {
            $color = Factory::init($c);
            $this->assertInstanceOf(Rgba::class, $color);
        }
    }

    /**
     * @group factory-init
     */
    public function testHslReturnType()
    {
        $color = Factory::init('hsl(200, 100%, 50%)');
        $this->assertInstanceOf(Hsl::class, $color);
    }

    /**
     * @group factory-init
     */
    public function testHslaReturnType()
    {
        foreach (['hsla(200, 100%, 50%, 0.5)', '200, 100%, 50%, 0.5'] as $c) {
            $color = Factory::init($c);
            $this->assertInstanceOf(Hsla::class, $color);
        }
    }

    /**
     * @group factory-init
     */
    public function testHsvReturnType()
    {
        $color = Factory::init('hsv(200, 100%, 50%)');
        $this->assertInstanceOf(Hsv::class, $color);
    }

    /**
     * @group factory-init
     */
    public function testAmbiguousString()
    {
        $this->expectException(\OzdemirBurak\Iris\Exceptions\AmbiguousColorString::class);
        Factory::init('100, 20%, 5%');
    }
}
