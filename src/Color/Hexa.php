<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Helpers\ColorString;
use OzdemirBurak\Iris\Helpers\DefinedColor;
use OzdemirBurak\Iris\Traits\AlphaTrait;
use OzdemirBurak\Iris\Traits\HexTrait;

class Hexa extends BaseColor
{
    use AlphaTrait, HexTrait;

    /**
     * @param string $code
     *
     * @return string|bool
     */
    protected function validate(string $code): bool|string
    {
        $color = ColorString::isTransparent($code) ? '00000000' : DefinedColor::find($code);
        $color = str_starts_with($color, '#') ? substr($color, 1) : $color;
        if (strlen($color) === 3 || strlen($color) === 4) {
            $color = implode('', array_map(fn (string $digit) => $digit . $digit, str_split($color)));
        }
        return preg_match('/^[a-f0-9]{6}([a-f0-9]{2})?$/i', $color) ? $color : false;
    }

    /**
     * @param string $color
     *
     * @return list<float|string>
     */
    protected function initialize(string $color): array
    {
        $parts = str_split($color, 2);
        [$this->red, $this->green, $this->blue] = $parts;
        $this->alpha = $this->alphaHexToFloat($parts[3] ?? 'ff');
        return $this->values();
    }

    /**
     * @return array{string, string, string, float}
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
        return clone $this;
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
        return $this->toHsl()->toHsla()->alpha($this->alphaRaw());
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
        return $this->hexToRgb();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgba
     */
    public function toRgba(): Rgba
    {
        return $this->toRgb()->toRgba()->alpha($this->alphaRaw());
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Cmyk
     */
    public function toCmyk(): Cmyk
    {
        return $this->toRgb()->toCmyk();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Oklch
     */
    public function toOklch(): Oklch
    {
        $oklch = $this->opaqueRgb()->toOklch();
        $oklch->alpha($this->alpha);
        return $oklch;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return '#' . implode('', [$this->red(), $this->green(), $this->blue(), $this->alphaFloatToHex($this->alphaRaw())]);
    }
}
