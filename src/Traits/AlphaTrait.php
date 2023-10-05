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
    public function alpha($alpha = null)
    {
        if ($alpha !== null) {
            $this->alpha = $this->localeSafeAlpha(min($alpha, 1));
            return $this;
        }
        return $this->localeSafeAlpha($this->alpha);
    }

    /**
     * @param float $alpha
     *
     * @return $this|float
     */
    public function localeSafeAlpha($alpha)
    {
        setlocale(LC_NUMERIC, 'C');

        $safeAlpha = round($alpha, 2);

        setlocale(LC_NUMERIC, 0);

        return $safeAlpha;
    }

    /**
     * @return string
     */
    protected function validationRules(): string
    {
        return '/^(\d{1,3}),(\d{1,3}),(\d{1,3}),(\d\.\d{1,})$/';
    }

    /**
     * @param $color
     *
     * @return string
     */
    protected function fixPrecision($color): string
    {
        if (strpos($color, ',') !== false) {
            $parts = explode(',', $color);
            $parts[3] = strpos($parts[3], '.') === false ? $parts[3] . '.0' : $parts[3];
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
        return dechex($alpha * 255);
    }
}
