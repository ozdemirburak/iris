<?php

namespace OzdemirBurak\Iris\Traits;

trait AlphaTrait
{
    /**
     * @var double
     */
    protected $alpha;

    /**
     * @param null $alpha
     *
     * @return $this|float
     */
    public function alpha($alpha = null): float|static
    {
        if ($alpha !== null) {
            $this->alpha = min(round($alpha, 2), 1);
            return $this;
        }
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
        return sprintf('%0.2F', hexdec($alpha) / 255);
    }

    /**
     * @param float $alpha
     * @return string
     */
    protected function alphaFloatToHex(float $alpha): string
    {
        return dechex((int) ($alpha * 255));
    }
}
