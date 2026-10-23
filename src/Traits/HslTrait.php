<?php

namespace OzdemirBurak\Iris\Traits;

use OzdemirBurak\Iris\Color\Rgb;

trait HslTrait
{
    use HsTrait;

    /**
     * @var float
     */
    protected float $lightness;

    /**
     * @param float|int|string|null $lightness a value out of range is clamped, a value that is not a number is ignored
     *
     * @return ($lightness is null ? float : static)
     */
    public function lightness($lightness = null): float|static
    {
        if ($lightness !== null) {
            if (is_numeric($lightness)) {
                $this->lightness = $this->limit((float) $lightness, 100, $this->lightness);
            }
            return $this;
        }
        return (float) $this->lightness;
    }

    /**
     * @return float
     */
    protected function lightnessOrValue(): float
    {
        return $this->lightness;
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    public function convertToRgb(): Rgb
    {
        [$h, $s, $l] = $this->valuesInUnitInterval();
        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) :
                $l + $s - $l * $s;
            $p = 2 * $l - $q;
            [$r, $g, $b] = [
                $this->hueToRgb($p, $q, $h + 1 / 3),
                $this->hueToRgb($p, $q, $h),
                $this->hueToRgb($p, $q, $h - 1 / 3)
            ];
        }
        $code = implode(',', [round($r * 255), round($g * 255), round($b * 255)]);
        return new Rgb($code);
    }

    /**
     * @param float $p
     * @param float $q
     * @param float $t
     *
     * @return float
     */
    protected function hueToRgb(float $p, float $q, float $t): float
    {
        if ($t < 0) {
            $t++;
        }
        if ($t > 1) {
            $t--;
        }
        if ($t < 1/6) {
            return $p + ($q - $p) * 6 * $t;
        }
        if ($t < 1/2) {
            return $q;
        }
        if ($t < 2/3) {
            return $p + ($q - $p) * (2/3 - $t) * 6;
        }
        return $p;
    }
}
