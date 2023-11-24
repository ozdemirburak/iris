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
use PHPUnit\Framework\TestCase;

class AlphaTest extends TestCase
{
    /**
     * @group alpha
     */
    public function testRgb()
    {
        [$rgba, $hex] = $this->getBaseValues();
        $this->assertEquals($hex, $rgba->toRgb()->toHex());
        $this->assertEquals($hex, $rgba->toHex());
    }
    /**
     * @group alpha
     */
    public function testHexa()
    {
        [$rgba, $hex] = $this->getBaseValues();
        $this->assertEquals($hex, $rgba->toHexa()->toHex());
    }
    /**
     * @group alpha
     */
    public function testHsl()
    {
        [$rgba, $hex] = $this->getBaseValues();
        $this->assertEquals($hex, $rgba->toHsl()->toHex());
    }

    /**
     * @group alpha
     */
    public function testHsla()
    {
        [$rgba, $hex] = $this->getBaseValues();
        $this->assertEquals($hex, $rgba->toHsla()->toHex());
    }

    /**
     * @group alpha
     */
    public function testIssueValues()
    {
        [$rgb, $hsl] = [new Rgb('127,127,127'), new Hsl('0, 0%, 49.8%')];
        $this->assertEquals($rgb->toHsl(), $hsl);
        [$rgba, $hex] = $this->getBaseValues();
        $this->assertEquals($rgba->toHsla(), new Hsla('0, 0%, 40%, 0.5'));

    }

    /**
     * @return array
     */
    protected function getBaseValues(): array
    {
        return [new Rgba('rgba(102, 102, 102, 0.5)'), new Hex('b2b2b2')];
    }
}
