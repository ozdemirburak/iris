<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Traits\HsTrait;

class Hsv extends BaseColor
{
    use HsTrait;

    /**
     * @var float
     */
    protected float $value;

    /**
     * @param string $color
     *
     * @return list<float>
     */
    protected function initialize(string $color): array
    {
        return [$this->hue, $this->saturation, $this->value] = array_map('floatval', explode(',', $color));
    }

    /**
     * @return array{float, string, string}
     */
    public function values(): array
    {
        return $this->getValues();
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
        return $this->toRgb()->toHex()->toHexa();
    }

    /**
     * Source: https://en.wikipedia.org/wiki/HSL_and_HSV#Interconversion
     *
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsl
     */
    public function toHsl(): Hsl
    {
        return new Hsl(implode(',', array_map(fn (float $value) => round($value, 1), $this->hslValues())));
    }

    /**
     * Not rounded, so that an operation on a color rounds only once, when it converts back to HSV
     *
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsl
     */
    protected function opaqueHsl(): Hsl
    {
        [$hue, $saturation, $lightness] = $this->hslValues();
        $hsl = new Hsl('0,0,0');
        $hsl->hue($hue);
        $hsl->saturation($saturation);
        $hsl->lightness($lightness);
        return $hsl;
    }

    /**
     * @return array{float, float, float} the hue in degrees, the saturation and the lightness in percent
     */
    private function hslValues(): array
    {
        [$h, $s, $v] = $this->valuesInUnitInterval();
        $l = $v * (1 - $s / 2);
        $m = min($l, 1 - $l);
        $s = $l > 0 && $l < 1 ? ($v - $l) / $m : 0;
        return [$h * 360, $s * 100, $l * 100];
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
     * @return \OzdemirBurak\Iris\Color\Hsv
     */
    public function toHsv(): Hsv
    {
        return clone $this;
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    public function toRgb(): Rgb
    {
        [$h, $s, $v] = $this->valuesInUnitInterval();
        $i = floor($h * 6);
        $f = $h * 6 - $i;
        $p = $v * (1 - $s);
        $q = $v * (1 - $f * $s);
        $t = $v * (1 - (1 - $f) * $s);
        [$r, $g, $b] = match ((int) $i % 6) {
            0 => [$v, $t, $p],
            1 => [$q, $v, $p],
            2 => [$p, $v, $t],
            3 => [$p, $q, $v],
            4 => [$t, $p, $v],
            default => [$v, $p, $q],
        };
        $code = implode(',', [round($r * 255), round($g * 255), round($b * 255)]);
        return new Rgb($code);
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
        return $this->toRgb()->toOklch();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'hsv(' . implode(',', $this->values()) . ')';
    }

    /**
     * @return float
     */
    protected function lightnessOrValue(): float
    {
        return $this->value;
    }

    /**
     * @param float|int|string|null $value a value out of range is clamped, a value that is not a number is ignored
     *
     * @return ($value is null ? float : static)
     */
    public function value($value = null): float|static
    {
        if ($value !== null) {
            if (is_numeric($value)) {
                $this->value = $this->limit((float) $value, 100, $this->value);
            }
            return $this;
        }
        return (float) $this->value;
    }
}
