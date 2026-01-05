<?php

namespace OzdemirBurak\Iris\Traits;

trait AlphaTrait
{
    /**
     * @var double
     */
    protected $alpha;

    /**
     * @param $alpha
     *
     * @return $this|float
     */
    public function alpha($alpha = null): float|static
    {
        if ($alpha !== null) {
            $this->alpha = min((float) $alpha, 1.0);
            return $this;
        }
        return round($this->alpha, 2);
    }

    /**
     * Get the raw alpha value without rounding (for internal conversions)
     *
     * @return float
     */
    protected function alphaRaw(): float
    {
        return $this->alpha;
    }

    /**
     * @param $color
     *
     * @return string
     */
    protected function fixPrecision($color): string
    {
        if (str_contains($color, ',')) {
            $parts = explode(',', $color);
            $parts[3] = !str_contains($parts[3], '.') ? $parts[3] . '.0' : $parts[3];
            $color = implode(',', $parts);
        }
        return $color;
    }

    /**
     * @param string $alpha
     * @return float
     */
    protected function alphaHexToFloat(string $alpha): float
    {
        return hexdec($alpha) / 255;
    }

    /**
     * @param float $alpha
     * @return string
     */
    protected function alphaFloatToHex(float $alpha): string
    {
        return str_pad(dechex((int) round($alpha * 255)), 2, '0', STR_PAD_LEFT);
    }
}
