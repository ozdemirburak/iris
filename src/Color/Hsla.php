<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Helpers\ColorString;
use OzdemirBurak\Iris\Helpers\DefinedColor;
use OzdemirBurak\Iris\Traits\AlphaTrait;
use OzdemirBurak\Iris\Traits\HslTrait;

class Hsla extends BaseColor
{
    use AlphaTrait, HslTrait;

    /**
     * @param string $code
     *
     * @return false|string
     */
    protected function validate(string $code): bool|string
    {
        if (ColorString::isTransparent($code)) {
            return '0,0,0,0';
        }
        // A color name comes from its exact hex value; the hsl column of the table is rounded
        if (($hex = DefinedColor::hex($code)) !== null) {
            return implode(',', [...array_map('floatval', (new Hex($hex))->toHsl()->getValues()), 1]);
        }
        if (($parts = ColorString::split($code, ['hsla', 'hsl'], 3, true)) === false) {
            return false;
        }
        [[$hue, $saturation, $lightness], $alpha] = $parts;
        return ColorString::join([
            ColorString::hue($hue),
            ColorString::percentage($saturation),
            ColorString::percentage($lightness),
            ColorString::alpha($alpha),
        ]);
    }

    /**
     * @param string $color
     *
     * @return list<float|string>
     */
    protected function initialize(string $color): array
    {
        [$this->hue, $this->saturation, $this->lightness, $this->alpha] = array_map('floatval', explode(',', $color));
        return $this->values();
    }

    /**
     * @return list<float|string>
     */
    public function values(): array
    {
        return array_merge($this->getValues(), [$this->alpha()]);
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsl
     */
    public function toHsl(): Hsl
    {
        return $this->toRgba()->toHsl();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgba
     */
    public function toRgba(): Rgba
    {
        return $this->convertToRgb()->toRgba()->alpha($this->alphaRaw());
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    public function toRgb(): Rgb
    {
        return $this->toRgba()->toRgb();
    }

    /**
     * @return \OzdemirBurak\Iris\Color\Hsla
     */
    public function toHsla(): Hsla
    {
        return clone $this;
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsv
     */
    public function toHsv(): Hsv
    {
        return $this->toRgba()->toHsv();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hex
     */
    public function toHex(): Hex
    {
        return $this->toRgba()->toHex();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hexa
     */
    public function toHexa(): Hexa
    {
        return $this->toRgba()->toHexa();
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
        return 'hsla(' . implode(',', $this->values()) . ')';
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    protected function opaqueRgb(): Rgb
    {
        return $this->convertToRgb();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsl
     */
    protected function opaqueHsl(): Hsl
    {
        $hsl = new Hsl('0,0,0');
        $hsl->hue($this->hue);
        $hsl->saturation($this->saturation);
        $hsl->lightness($this->lightness);
        return $hsl;
    }
}
