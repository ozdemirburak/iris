<?php

namespace OzdemirBurak\Iris\Tests\Color;

use OzdemirBurak\Iris\Color\Cmyk;
use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hexa;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Hsv;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Color\Rgba;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class AlphaTest extends TestCase
{
    #[Group('alpha')]
    public function testRgb()
    {
        [$rgba, $hex] = $this->getBaseValues();
        $this->assertEquals($hex, $rgba->toRgb()->toHex());
        $this->assertEquals($hex, $rgba->toHex());
    }

    #[Group('alpha')]
    public function testHexa()
    {
        [$rgba, $hex] = $this->getBaseValues();
        // toHexa preserves original RGB values, toHex applies alpha compositing
        $this->assertEquals(new Hex('666666'), $rgba->toHexa()->toHex());
        // When going through toRgb() first, alpha compositing is applied
        $this->assertEquals($hex, $rgba->toRgb()->toHex());
    }

    #[Group('alpha')]
    public function testHsl()
    {
        [$rgba, $hex] = $this->getBaseValues();
        $this->assertEquals($hex, $rgba->toHsl()->toHex());
    }

    #[Group('alpha')]
    public function testHsla()
    {
        [$rgba, $hex] = $this->getBaseValues();
        $this->assertEquals($hex, $rgba->toHsla()->toHex());
    }

    #[Group('alpha')]
    public function testIssueValues()
    {
        [$rgb, $hsl] = [new Rgb('127,127,127'), new Hsl('0, 0%, 49.8%')];
        $this->assertEquals($rgb->toHsl(), $hsl);
        [$rgba, $hex] = $this->getBaseValues();
        $this->assertEquals($rgba->toHsla(), new Hsla('0, 0%, 40%, 0.5'));

    }

    #[Group('alpha')]
    public function testNegativeAlphaIsClampedToZero()
    {
        foreach ($this->alphaColors() as $color) {
            $color->alpha(-0.3);
            $this->assertEquals(0.0, $color->alpha(), get_class($color));
            // the serialized string must be a valid input to its own constructor
            $class = get_class($color);
            $this->assertEquals(0.0, (new $class((string) $color))->alpha());
        }
    }

    #[Group('alpha')]
    public function testAlphaAboveOneStillClampsToOne()
    {
        foreach ($this->alphaColors() as $color) {
            $color->alpha(1.7);
            $this->assertEquals(1.0, $color->alpha(), get_class($color));
            $class = get_class($color);
            $this->assertEquals(1.0, (new $class((string) $color))->alpha());
        }
    }

    #[Group('alpha')]
    public function testInRangeAlphaPassesThroughUnchanged()
    {
        foreach ([0.0, 0.5, 1.0] as $value) {
            foreach ($this->alphaColors() as $color) {
                $color->alpha($value);
                $this->assertEquals($value, $color->alpha(), get_class($color));
            }
        }
    }

    protected function alphaColors(): array
    {
        return [new Rgba('10,20,30,0.5'), new Hsla('120,50%,50%,0.5'), new Hexa('112233ff')];
    }

    protected function getBaseValues(): array
    {
        return [new Rgba('rgba(102, 102, 102, 0.5)'), new Hex('b2b2b2')];
    }
}
