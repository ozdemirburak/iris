<?php

namespace OzdemirBurak\Iris\Tests\Color;

use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsv;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Exceptions\InvalidColorException;
use PHPUnit\Framework\TestCase;

class HsvTest extends TestCase
{
    /**
     * @group rgb-construction
     */
    public function testDigitString()
    {
        $hsv = new Hsv('hsv(300,100%,100%)');
        $this->validateFuschia($hsv);
    }

    /**
     * @group rgb-construction
     */
    public function testPredefinedString()
    {
        $hsv = new Hsv('FUSCHIA');
        $this->validateFuschia($hsv);
    }

    /**
     * @group rgb-construction
     */
    public function testInvalidColor()
    {
        try {
            $hsv = new Hsv('333,0,666');
        } catch (InvalidColorException $e) {
            return $this->assertEquals($e->getMessage(), 'Invalid HSV value.');
        }
        $this->fail('Exception has not been raised.');
    }

    /**
     * @param \OzdemirBurak\Iris\Color\Hsv $hsv
     */
    private function validateFuschia(Hsv $hsv)
    {
        $this->assertEquals('300', $hsv->hue());
        $this->assertEquals('100', $hsv->saturation());
        $this->assertEquals('100', $hsv->value());
        $this->assertEquals([300, '100%', '100%'], $hsv->values());
        $this->assertEquals([300/360, 100/100, 100/100], $hsv->valuesInUnitInterval());
        $this->assertEquals('hsv(300,100%,100%)', $hsv);
        $this->assertEquals(new Hex('ff00ff'), $hsv->toHex());
        $this->assertEquals(new Hsl('300,100,50'), $hsv->toHsl());
        $this->assertEquals(new Hsv('300,100,100'), $hsv->toHsv());
        $this->assertEquals(new Rgb('255,0,255'), $hsv->toRgb());
    }
}
