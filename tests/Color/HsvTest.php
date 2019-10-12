<?php

namespace OzdemirBurak\Iris\Tests\Color;

use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Hsv;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Color\Rgba;
use PHPUnit\Framework\TestCase;

class HsvTest extends TestCase
{
    /**
     * @group rgb-construction
     */
    public function testDigitString()
    {
        $hsv = new Hsv('hsv(300,100%,100%)');
        $this->validateFuchsia($hsv);
    }

    /**
     * @group rgb-construction
     */
    public function testPredefinedString()
    {
        $hsv = new Hsv('FUCHSIA');
        $this->validateFuchsia($hsv);
    }

    /**
     * @group rgb-construction
     */
    public function testInvalidColor()
    {
        $this->expectException(\OzdemirBurak\Iris\Exceptions\InvalidColorException::class);
        $this->expectExceptionMessage('Invalid HSV value');
        new Hsv('333,0,666');
    }

    /**
     * @dataProvider hsvToRgbPairs
     * @param string $hsv
     * @param string $rgb
     * @param string $name
     */
    public function testCanConvertToRgb($hsv, $rgb, $name)
    {
        $this->assertEquals(new Rgb($rgb), (new Hsv($hsv))->toRgb(), "Can convert $name HSV to RGB");
    }

    public function hsvToRgbPairs()
    {
        return [
            ['hsv(0,0%,0%)', '0,0,0', 'black'],
            ['hsv(0,0%,100%)', '255,255,255', 'white'],
            ['hsv(0,100%,100%)', '255,0,0', 'red'],
            ['hsv(120,100%,100%)', '0,255,0', 'lime'],
            ['hsv(240,100%,100%)', '0,0,255', 'blue'],
            ['hsv(60,100%,100%)', '255,255,0', 'yellow'],
            ['hsv(180,100%,100%)', '0,255,255', 'cyan'],
            ['hsv(300,100%,100%)', '255,0,255', 'magenta'],
            ['hsv(0,0%,75%)', '191,191,191', 'silver'],
            ['hsv(0,0%,50%)', '128,128,128', 'gray'],
            ['hsv(0,100%,50%)', '128,0,0', 'maroon'],
            ['hsv(60,100%,50%)', '128,128,0', 'olive'],
            ['hsv(120,100%,50%)', '0,128,0', 'green'],
            ['hsv(300,100%,50%)', '128,0,128', 'purple'],
            ['hsv(180,100%,50%)', '0,128,128', 'teal'],
            ['hsv(240,100%,50%)', '0,0,128', 'navy'],
        ];
    }

    public function testCanReadAndWriteComponentsSeparately()
    {
        $hsv = new Hsv('hsv(0,0%,0%');
        $hsv->hue(41);
        $this->assertEquals(41, $hsv->hue());
        $hsv->saturation(42);
        $this->assertEquals(42, $hsv->saturation());
        $hsv->value(43);
        $this->assertEquals(43, $hsv->value());
        $this->assertEquals('hsv(41,42%,43%)', (string) $hsv);
    }

    /**
     * @param \OzdemirBurak\Iris\Color\Hsv $hsv
     */
    private function validateFuchsia(Hsv $hsv)
    {
        $this->assertEquals('300', $hsv->hue());
        $this->assertEquals('100', $hsv->saturation());
        $this->assertEquals('100', $hsv->value());
        $this->assertEquals([300, '100%', '100%'], $hsv->values());
        $this->assertEquals([300/360, 100/100, 100/100], $hsv->valuesInUnitInterval());
        $this->assertEquals('hsv(300,100%,100%)', $hsv);
        $this->assertEquals(new Hex('ff00ff'), $hsv->toHex());
        $this->assertEquals(new Hsl('300,100,50'), $hsv->toHsl());
        $this->assertEquals(new Hsla('300,100,50,1.0'), $hsv->toHsla());
        $this->assertEquals(new Hsv('300,100,100'), $hsv->toHsv());
        $this->assertEquals(new Rgb('255,0,255'), $hsv->toRgb());
        $this->assertEquals(new Rgba('255,0,255,1.0'), $hsv->toRgba());
    }
}
