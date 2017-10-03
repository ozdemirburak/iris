<?php

namespace OzdemirBurak\Iris\Tests\Color;

use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Hsv;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Color\Rgba;
use OzdemirBurak\Iris\Exceptions\InvalidColorException;
use PHPUnit\Framework\TestCase;

class RgbTest extends TestCase
{
    /**
     * @group rgb-construction
     */
    public function testDigitString()
    {
        $rgb = new Rgb('rgb(255, 0, 255)');
        $this->validateFuschia($rgb);
    }

    /**
     * @group rgb-construction
     */
    public function testPredefinedString()
    {
        $rgb = new Rgb('FUSCHIA');
        $this->validateFuschia($rgb);
    }

    /**
     * @group rgb-construction
     */
    public function testInvalidColor()
    {
        try {
            $rgb = new Rgb('333,0,666');
        } catch (InvalidColorException $e) {
            return $this->assertContains('Invalid RGB value', $e->getMessage());
        }
        $this->fail('Exception has not been raised.');
    }

    /**
     * @param \OzdemirBurak\Iris\Color\Rgb $rgb
     */
    private function validateFuschia(Rgb $rgb)
    {
        $this->assertEquals(255, $rgb->red());
        $this->assertEquals(0, $rgb->green());
        $this->assertEquals(255, $rgb->blue());
        $this->assertEquals([255, 0, 255], $rgb->values());
        $this->assertEquals('rgb(255,0,255)', $rgb);
        $this->assertEquals(new Hex('ff00ff'), $rgb->toHex());
        $this->assertEquals(new Hsl('300,100,50'), $rgb->toHsl());
        $this->assertEquals(new Hsla('300,100,50,1.0'), $rgb->toHsla());
        $this->assertEquals(new Hsv('300,100,100'), $rgb->toHsv());
        $this->assertEquals(new Rgb('255,0,255'), $rgb->toRgb());
        $this->assertEquals(new Rgba('255,0,255,1.0'), $rgb->toRgba());
    }
}
