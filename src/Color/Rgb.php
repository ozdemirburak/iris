<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Helpers\ColorString;
use OzdemirBurak\Iris\Helpers\DefinedColor;
use OzdemirBurak\Iris\Traits\RgbTrait;

class Rgb extends BaseColor
{
    use RgbTrait;

    /**
     * @param string $code
     *
     * @return string|bool
     */
    protected function validate(string $code): bool|string
    {
        // Fast path for the plain "r,g,b" form that the conversions create
        if (preg_match('/^(\d{1,3}),(\d{1,3}),(\d{1,3})$/', $code, $matches)) {
            return max((int) $matches[1], (int) $matches[2], (int) $matches[3]) <= 255 ? $code : false;
        }
        if (($parts = ColorString::split(DefinedColor::find($code, 1), ['rgb'], 3, false)) === false) {
            return false;
        }
        return ColorString::join(array_map([ColorString::class, 'channel'], $parts[0]));
    }

    /**
     * @param string $color
     *
     * @return list<int>
     */
    protected function initialize(string $color): array
    {
        return [$this->red, $this->green, $this->blue] = array_map('intval', explode(',', $color));
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hex
     */
    public function toHex(): Hex
    {
        $code = sprintf('%02x%02x%02x', $this->red(), $this->green(), $this->blue());
        return new Hex($code);
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hexa
     */
    public function toHexa(): Hexa
    {
        return $this->toHex()->toHexa();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsl
     */
    public function toHsl(): Hsl
    {
        [$h, $s, $l] = $this->getHslValues();
        $code = implode(',', [round($h * 360, 1), round($s * 100, 1), round($l * 100, 1)]);
        return new Hsl($code);
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsla
     */
    public function toHsla(): Hsla
    {
        return $this->toHsl()->toHsla();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsv
     */
    public function toHsv(): Hsv
    {
        [$r, $g, $b, $min, $max] = $this->getHValues();
        $v = $max;
        $d = $max - $min;
        $s = $max == 0 ? 0 : $d / $max;
        $h = $max === $min ? 0 : $this->getH($max, $r, $g, $b, $d);
        $code = implode(',', [round($h * 360, 1), round($s * 100, 1), round($v * 100, 1)]);
        return new Hsv($code);
    }

    /**
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    public function toRgb(): Rgb
    {
        return clone $this;
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgba
     */
    public function toRgba(): Rgba
    {
        return new Rgba(implode(',', array_merge($this->values(), ['1.0'])));
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Cmyk
     */
    public function toCmyk(): Cmyk
    {
        [$r, $g, $b] = $this->rgbValues();
        $k = 1 - (max($r, $g, $b) / 255);
        $c = $k < 1 ? (1 - $r / 255 - $k) / (1 - $k) : 0;
        $m = $k < 1 ? (1 - $g / 255 - $k) / (1 - $k) : 0;
        $y = $k < 1 ? (1 - $b / 255 - $k) / (1 - $k) : 0;
        // One decimal, so that RGB to CMYK to RGB gives the same color for all 16.7 million colors
        $code = implode(',', [round($c * 100, 1), round($m * 100, 1), round($y * 100, 1), round($k * 100, 1)]);
        return new Cmyk($code);
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Oklch
     */
    public function toOklch(): Oklch
    {
        return Oklch::fromRgb($this->red(), $this->green(), $this->blue());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'rgb(' . implode(',', $this->values()) . ')';
    }
}
