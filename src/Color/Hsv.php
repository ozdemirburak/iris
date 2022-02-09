<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Traits\HsTrait;

class Hsv extends BaseColor
{
    use HsTrait;

    /**
     * @var int
     */
    protected $value;

    /**
     * @param string $color
     *
     * @return array
     */
    protected function initialize(string $color): array
    {
        return [$this->hue, $this->saturation, $this->value] = explode(',', $color);
    }

    /**
     * @return array
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
        [$h, $s, $v] = $this->valuesInUnitInterval();
        $l = $v * (1 - $s / 2);
        $m = min($l, 1 - $l);
        $s = $l && $l < 1 ? ($v - $l) / $m : 0;
        $code = implode(',', [round($h * 360), round($s * 100), round($l * 100)]);
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
     * @return \OzdemirBurak\Iris\Color\Hsv
     */
    public function toHsv(): Hsv
    {
        return $this;
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
        switch ($i % 6) {
            case 0:
                [$r, $g, $b] = [$v, $t, $p];
                break;
            case 1:
                [$r, $g, $b] = [$q, $v, $p];
                break;
            case 2:
                [$r, $g, $b] = [$p, $v, $t];
                break;
            case 3:
                [$r, $g, $b] = [$p, $q, $v];
                break;
            case 4:
                [$r, $g, $b] = [$t, $p, $v];
                break;
            case 5:
                [$r, $g, $b] = [$v, $p, $q];
                break;
        }
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
     * @return string
     */
    public function __toString(): string
    {
        return 'hsv(' . implode(',', $this->values()) . ')';
    }

    /**
     * @param int|string $value
     *
     * @return int|$this
     */
    public function value($value = null)
    {
        if (is_numeric($value)) {
            $this->value = $value >= 0 && $value <= 100 ? $value : $this->value;
            return $this;
        }
        return (int) $this->value;
    }
}
