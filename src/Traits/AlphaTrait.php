<?php

namespace OzdemirBurak\Iris\Traits;

trait AlphaTrait
{
    /**
     * @var float
     */
    protected float $alpha;

    /**
     * @param float|int|string|null $alpha from 0 to 1, a value out of range is clamped, a value that is not a number is ignored
     *
     * @return ($alpha is null ? float : static)
     */
    public function alpha($alpha = null): float|static
    {
        if ($alpha !== null) {
            if (is_numeric($alpha)) {
                $this->alpha = $this->limit((float) $alpha, 1, $this->alpha);
            }
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
