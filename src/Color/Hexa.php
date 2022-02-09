<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Helpers\DefinedColor;
use OzdemirBurak\Iris\Traits\AlphaTrait;
use OzdemirBurak\Iris\Traits\RgbTrait;

class Hexa extends BaseColor
{
    use AlphaTrait, RgbTrait;

    /**
     * @param string $code
     *
     * @return string|bool
     */
    protected function validate(string $code)
    {
        $color = str_replace('#', '', DefinedColor::find($code));
        return preg_match('/^[a-f0-9]{6}([a-f0-9]{2})?$/i', $color) ? $color : false;
    }

    /**
     * @param string $color
     *
     * @return array
     */
    protected function initialize(string $color): array
    {
        [$this->red, $this->green, $this->blue, $this->alpha] = array_merge(str_split($color, 2), ['ff']);
        $this->alpha = $this->alphaHexToFloat($this->alpha ?? 'ff');
        return $this->values();
    }

    /**
     * @return array
     */
    public function values(): array
    {
        return [
            $this->red(),
            $this->green(),
            $this->blue(),
            $this->alpha()
        ];
    }

    /**
     * @return \OzdemirBurak\Iris\Color\Hex
     */
    public function toHex(): Hex
    {
        return new Hex(implode([$this->red(), $this->green(), $this->blue()]));
    }

    /**
     * @return \OzdemirBurak\Iris\Color\Hexa
     */
    public function toHexa(): Hexa
    {
        return $this;
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
        return $this->toHsl()->toHsla()->alpha($this->alpha());
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
        $rgb = implode(',', array_map('hexdec', [$this->red(), $this->green(), $this->blue()]));
        return new Rgb($rgb);
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgba
     */
    public function toRgba(): Rgba
    {
        return $this->toRgb()->toRgba()->alpha($this->alpha());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        [$r, $g, $b, $a] = $this->values();
        return '#' . implode('', [$r, $g, $b, $this->alphaFloatToHex($a)]);
    }
}
