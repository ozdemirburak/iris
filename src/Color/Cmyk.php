<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Helpers\ColorString;
use OzdemirBurak\Iris\Helpers\DefinedColor;
use OzdemirBurak\Iris\Traits\CmykTrait;

class Cmyk extends BaseColor
{
    use CmykTrait;

    /**
     * @param string $code
     *
     * @return string|bool
     */
    protected function validate(string $code): bool|string
    {
        // A color name comes from its exact hex value; the cmyk column of the table is rounded
        if (($hex = DefinedColor::hex($code)) !== null) {
            return implode(',', (new Hex($hex))->toCmyk()->values());
        }
        if (($parts = ColorString::split($code, ['cmyk'], 4, false)) === false) {
            return false;
        }
        return ColorString::join(array_map([ColorString::class, 'percentage'], $parts[0]));
    }

    /**
     * @param string $color
     *
     * @return list<float>
     */
    protected function initialize(string $color): array
    {
        return [$this->cyan, $this->magenta, $this->yellow, $this->black] = array_map('floatval', explode(',', $color));
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hex
     */
    public function toHex(): Hex
    {
        return $this->toRgb()->toHex();
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
        return $this->toRgb()->toHsl();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsla
     */
    public function toHsla(): Hsla
    {
        return $this->toRgb()->toHsla();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsv
     */
    public function toHsv(): Hsv
    {
        return $this->toRgb()->toHsv();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    public function toRgb(): Rgb
    {
        [$c, $m, $y, $k] = $this->values();
        $r = round(255 * (1 - ($c / 100)) * (1 - ($k / 100)));
        $g = round(255 * (1 - ($m / 100)) * (1 - ($k / 100)));
        $b = round(255 * (1 - ($y / 100)) * (1 - ($k / 100)));
        $code = implode(',', [$r, $g, $b]);
        return new Rgb($code);
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Cmyk
     */
    public function toCmyk(): Cmyk
    {
        return clone $this;
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgba
     */
    public function toRgba(): Rgba
    {
        return $this->toRgb()->toRgba();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Oklch
     */
    public function toOklch(): Oklch
    {
        return $this->toRgb()->toOklch();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'cmyk(' . implode(',', $this->values()) . ')';
    }
}