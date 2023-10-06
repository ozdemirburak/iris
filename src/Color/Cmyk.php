<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
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
        $color = str_replace(['cmyk', '(', ')', ' ', '%'], '', DefinedColor::find($code, 4));
        if (preg_match('/^(\d{1,3}),(\d{1,3}),(\d{1,3}),(\d{1,3})$/', $color, $matches)) {
            if ($matches[1] > 100 || $matches[2] > 100 || $matches[3] > 100 || $matches[4] > 100) {
                return false;
            }
            return $color;
        }
        return false;
    }

    /**
     * @param string $color
     *
     * @return array
     */
    protected function initialize(string $color): array
    {
        return [$this->cyan, $this->magenta, $this->yellow, $this->black] = explode(',', $color);
    }

    /**
     * @return array
     */
    public function values(): array
    {
        return [
            $this->cyan(),
            $this->magenta(),
            $this->yellow(),
            $this->black(),
        ];
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
        $r = 255 * (1 - ($c / 100)) * (1 - ($k / 100));
        $g = 255 * (1 - ($m / 100)) * (1 - ($k / 100));
        $b = 255 * (1 - ($y / 100)) * (1 - ($k / 100));
        $code = implode(',', [$r, $g, $b]);
        return new Rgb($code);
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Cmyk
     */
    public function toCmyk(): Cmyk
    {
        return $this;
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
     * @return string
     */
    public function __toString(): string
    {
        return 'cmyk(' . implode(',', $this->values()) . ')';
    }
}