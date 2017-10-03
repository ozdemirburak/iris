<?php


namespace OzdemirBurak\Iris\Tests\Color;

use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Rgba;
use OzdemirBurak\Iris\Exceptions\InvalidColorException;
use PHPUnit\Framework\TestCase;

class HslaTest extends TestCase
{
    /**
     * @group hsla-construction
     */
    public function testDigitString()
    {
        $hsla = new Hsla('hsla(150,100%,50%,0.3)');
        $this->assertEquals('150', $hsla->hue());
        $this->assertEquals('100', $hsla->saturation());
        $this->assertEquals('50', $hsla->lightness());
        $this->assertEquals(0.3, $hsla->alpha());
        $this->assertEquals([150, '100%', '50%', 0.3], $hsla->values());
        $hsla->alpha(0);
        $this->assertEquals(0, $hsla->alpha());
        $this->assertEquals('hsla(150,100%,50%,0)', $hsla->__toString());
    }

    /**
     * @group hsla-construction
     */
    public function testPredefinedString()
    {
        $hsla = new Hsla('FUSCHIA');
        $this->assertEquals('300', $hsla->hue());
        $this->assertEquals('100', $hsla->saturation());
        $this->assertEquals('50', $hsla->lightness());
        $this->assertEquals(1, $hsla->alpha());
        $this->assertEquals([300, '100%', '50%', 1], $hsla->values());
    }

    /**
     * @group hsla-construction
     */
    public function testInvalidColor()
    {
        try {
            new Hsla('hsla(150,100%,50%,0.3.3,4)');
        } catch (InvalidColorException $e) {
            $this->assertContains('Invalid HSLA value', $e->getMessage());
            return ;
        }
        $this->fail('Exception has not been raised.');
    }

    /**
     * @group hsla-conversion
     */
    public function testHslaConversion()
    {
        $hsla = new Hsla('hsla(150,100%,50%,0.3)');
        $this->assertEquals(new Hex('b2ffd8'), $hsla->toHex());
        $this->assertEquals(new Rgba('0,255,128,0.3'), $hsla->toRgba());
    }
}
