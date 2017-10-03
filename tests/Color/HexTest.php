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

class HexTest extends TestCase
{
    /**
     * @group hex-construction
     */
    public function testSixDigitString()
    {
        $hex = new Hex('#ff00ff');
        $this->validateFuschia($hex);
    }

    /**
     * @group hex-construction
     */
    public function testThreeDigitString()
    {
        $hex = new Hex('#f0f');
        $this->validateFuschia($hex);
    }

    /**
     * @group hex-construction
     */
    public function testPredefinedString()
    {
        $hex = new Hex('FUSCHIA');
        $this->validateFuschia($hex);
    }

    /**
     * @group hex-construction
     */
    public function testInvalidColor()
    {
        try {
            $hex = new Hex('66Z');
        } catch (InvalidColorException $e) {
            return $this->assertContains('Invalid HEX value', $e->getMessage());
        }
        $this->fail('Exception has not been raised.');
    }

    /**
     * @param \OzdemirBurak\Iris\Color\Hex $hex
     */
    private function validateFuschia(Hex $hex)
    {
        $this->assertEquals('ff', $hex->red());
        $this->assertEquals('00', $hex->green());
        $this->assertEquals('ff', $hex->blue());
        $this->assertEquals('#ff00ff', $hex);
        $this->assertEquals(['ff', '00', 'ff'], $hex->values());
        $this->assertEquals(new Hex('ff00ff'), $hex->toHex());
        $this->assertEquals(new Hsl('300,100,50'), $hex->toHsl());
        $this->assertEquals(new Hsla('300,100,50,1.0'), $hex->toHsla());
        $this->assertEquals(new Hsv('300,100,100'), $hex->toHsv());
        $this->assertEquals(new Rgb('255,0,255'), $hex->toRgb());
        $this->assertEquals(new Rgba('255,0,255,1.0'), $hex->toRgba());
        $this->assertEquals(new Hex('#ff00ff'), $hex->saturate(10));
    }
}
