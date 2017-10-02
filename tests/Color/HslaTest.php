<?php


namespace OzdemirBurak\Iris\Tests\Color;


use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Exceptions\InvalidColorException;
use PHPUnit\Framework\TestCase;

class HslaTest extends TestCase
{
    /**
     * @group rgb-construction
     */
    public function testDigitString()
    {
        $hsl = new Hsla('hsla(150,100%,50%,0.3)');
        $this->assertEquals('150', $hsl->hue());
        $this->assertEquals('100', $hsl->saturation());
        $this->assertEquals('50', $hsl->lightness());
        $this->assertEquals([150, '100%', '50%', 0.3], $hsl->values());
    }

    /**
     * @group rgb-construction
     */
    public function testPredefinedString()
    {
        $hsl = new Hsla('FUSCHIA');
        $this->assertEquals('300', $hsl->hue());
        $this->assertEquals('100', $hsl->saturation());
        $this->assertEquals('50', $hsl->lightness());
        $this->assertEquals([300, '100%', '50%',0], $hsl->values());
    }

    /**
     * @group rgb-construction
     */
    public function testInvalidColor()
    {
        try {
            new Hsla('hsla(300,100%,50%,1.2)');
        } catch (InvalidColorException $e) {
            $this->assertEquals($e->getMessage(), 'Invalid HSLA value.');
            return ;
        }
        $this->fail('Exception has not been raised.');
    }

    /** @test */
    public function can_convert_to_string()
    {
        $hsl = new Hsla('hsla(150,100%,50%,0.3)');
        $this->assertEquals('hsla(150,100%,50%,0.3)', $hsl->__toString());
    }

    /** @test */
    public function can_convert_to_rgba()
    {
        $hsl = new Hsla('hsla(150,100%,50%,0.3)');
        $rgba = $hsl->toRgba();
        $this->assertEquals('rgba(0,255,128,0.3)', $rgba->__toString());
    }
}