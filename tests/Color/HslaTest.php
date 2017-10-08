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
    public function testFuschiaString()
    {
        $hsla = new Hsla('FUSCHIA');
        $this->validateFuschia($hsla);
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
     * @group hsla-construction
     */
    public function testGarbageColor()
    {
        try {
            new Hsla('hsla(361,1%,1%,0.3)');
        } catch (InvalidColorException $e) {
            $this->assertContains('Invalid HSLA value', $e->getMessage());
            return;
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


    /**
     * @param \OzdemirBurak\Iris\Color\Hsla $hsla
     */
    private function validateFuschia(Hsla $hsla)
    {
        $this->assertEquals(new Hex('ff00ff'), $hsla->toHex());
        $this->assertEquals(new Hsl('300,100,50'), $hsla->toHsl());
        $this->assertEquals(new Hsla('300,100,50,1.0'), $hsla->toHsla());
        $this->assertEquals(new Hsv('300,100,100'), $hsla->toHsv());
        $this->assertEquals(new Rgb('255,0,255'), $hsla->toRgb());
        $this->assertEquals(new Rgba('255,0,255,1.0'), $hsla->toRgba());
    }
}
