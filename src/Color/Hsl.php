<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Traits\HslTrait;

class Hsl extends BaseColor
{
    use HslTrait;

    /**
     * @param string $color
     *
     * @return array
     */
    protected function initialize($color)
    {
        return [$this->hue, $this->saturation, $this->lightness] = explode(',', $color);
    }

    /**
     * @return array
     */
    public function values()
    {
        return $this->getValues();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hex
     */
    public function toHex()
    {
        return $this->toRgb()->toHex();
    }

    /**
     * @return \OzdemirBurak\Iris\Color\Hsl
     */
    public function toHsl()
    {
        return $this;
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsla
     */
    public function toHsla()
    {
        return new Hsla(implode(',', array_merge($this->values(), [1.0])));
    }

    /**
     * Source: https://en.wikipedia.org/wiki/HSL_and_HSV#Interconversion
     *
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsv
     */
    public function toHsv()
    {
        [$h, $s, $l] = $this->valuesInUnitInterval();
        $v = $s * min($l, 1 - $l) + $l;
        $s = $v ? 2 * (1 - $l / $v) : 0;
        $code = implode(',', [round($h * 360), round($s * 100), round($v * 100)]);
        return new Hsv($code);
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    public function toRgb()
    {
        return $this->convertToRgb();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgba
     */
    public function toRgba()
    {
        return $this->toRgb()->toRgba();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'hsl(' . implode(',', $this->values()) . ')';
    }
}
