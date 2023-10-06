<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Helpers\DefinedColor;
use OzdemirBurak\Iris\Traits\RgbTrait;

class Rgb extends BaseColor
{
    use RgbTrait;

    /**
     * @var bool
     */
    protected bool $castsInteger = true;

    /**
     * @param string $code
     *
     * @return string|bool
     */
    protected function validate(string $code): bool|string
    {
        $color = str_replace(['rgb', '(', ')', ' '], '', DefinedColor::find($code, 1));
        if (preg_match('/^(\d{1,3}),(\d{1,3}),(\d{1,3})$/', $color, $matches)) {
            if ($matches[1] > 255 || $matches[2] > 255 || $matches[3] > 255) {
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
        return [$this->red, $this->green, $this->blue] = explode(',', $color);
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
        [$r, $g, $b, $min, $max] = $this->getHValues();
        $l = ($max + $min) / 2;
        if ($max === $min) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            $h = $this->getH($max, $r, $g, $b, $d);
        }
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
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsv
     */
    public function toHsv(): Hsv
    {
        [$r, $g, $b, $min, $max] = $this->getHValues();
        $v = $max;
        $d = $max - $min;
        $s = $max === 0 ? 0 : $d / $max;
        $h = $max === $min ? 0 : $this->getH($max, $r, $g, $b, $d);
        $code = implode(',', [round($h * 360), round($s * 100), round($v * 100)]);
        return new Hsv($code);
    }

    /**
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    public function toRgb(): Rgb
    {
        return $this;
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
        [$r, $g, $b] = $this->values();
        $k = 1 - (max($r, $g, $b) / 255);
        $c = (1 - $r / 255 - $k) / (1 - $k);
        $m = (1 - $g / 255 - $k) / (1 - $k);
        $y = (1 - $b / 255 - $k) / (1 - $k);
        $code = implode(',', [$c * 100, $m * 100, $y * 100, $k * 100]);
        return new Cmyk($code);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'rgb(' . implode(',', $this->values()) . ')';
    }

    /**
     * @param float $max
     * @param float $r
     * @param float $g
     * @param float $b
     * @param float $d
     *
     * @return float
     */
    private function getH($max, $r, $g, $b, $d): float
    {
        $h = match ($max) {
            $r => ($g - $b) / $d + ($g < $b ? 6 : 0),
            $g => ($b - $r) / $d + 2,
            $b => ($r - $g) / $d + 4,
            default => $max,
        };
        return $h / 6;
    }

    /**
     * @return array
     */
    private function getHValues(): array
    {
        [$r, $g, $b] = $values = array_map(function ($value) {
            return $value / 255;
        }, $this->values());
        [$min, $max] = [min($values), max($values)];
        return [$r, $g, $b, $min, $max];
    }
}
