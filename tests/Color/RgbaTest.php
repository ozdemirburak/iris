<?php


namespace OzdemirBurak\Iris\Tests\Color;

use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Rgba;
use OzdemirBurak\Iris\Exceptions\InvalidColorException;
use PHPUnit\Framework\TestCase;

class RgbaTest extends TestCase
{
    /**
     * @group rgba-construction
     */
    public function testDigitString()
    {
        $rgba = new Rgba('rgba(255,0,0,0.3)');
        $this->assertEquals(255, $rgba->red());
        $this->assertEquals(0, $rgba->blue());
        $this->assertEquals(0, $rgba->green());
        $this->assertEquals(0.3, $rgba->alpha());
        $this->assertEquals([255, 0, 0, 0.3], $rgba->values());
    }

    /**
     * @group rgba-construction
     */
    public function testPredefinedString()
    {
        $rgba = new Rgba('FUSCHIA');
        $this->assertEquals(255, $rgba->red());
        $this->assertEquals(0, $rgba->green());
        $this->assertEquals(255, $rgba->blue());
        $this->assertEquals(1, $rgba->alpha());
        $this->assertEquals([255, 0, 255, 1], $rgba->values());
    }

    /**
     * @group rgba-construction
     */
    public function testInvalidColor()
    {
        try {
            new Rgba('rgba(255,0,0,1.2)');
        } catch (InvalidColorException $e) {
            $this->assertContains('Invalid RGBA value', $e->getMessage());
            return;
        }
        try {
            new Rgba('rgba(255,0,0,1.2,0.2.3)'); // Invalid string.
        } catch (InvalidColorException $e) {
            $this->assertContains('Invalid RGBA value', $e->getMessage());
            return;
        }
        $this->fail('Exception has not been raised.');
    }

    /**
     * @group rgba-conversion
     */
    public function testRgbaConversion()
    {
        $rgba = new Rgba('rgba(11,22,33,0.2)');
        $this->assertEquals(new Hex('ced0d2'), $rgba->toHex());
        $rgba = new Rgba('rgba(93,111,222,0.33)');
        $this->assertEquals(new Hex('a7add1'), $rgba->background((new Hex('ccc'))->toRgb())->toHex());
    }
}
