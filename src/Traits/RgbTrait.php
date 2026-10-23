<?php

namespace OzdemirBurak\Iris\Traits;

/**
 * Integer RGB channels, from 0 to 255
 */
trait RgbTrait
{
    /**
     * @var int
     */
    protected int $red;

    /**
     * @var int
     */
    protected int $green;

    /**
     * @var int
     */
    protected int $blue;

    /**
     * @param float|int|string|null $red from 0 to 255, a value out of range is clamped
     *
     * @return ($red is null ? int : static)
     */
    public function red($red = null): int|static
    {
        if ($red !== null) {
            $this->red = $this->toChannel($red, $this->red);
            return $this;
        }
        return $this->red;
    }

    /**
     * @param float|int|string|null $green from 0 to 255, a value out of range is clamped
     *
     * @return ($green is null ? int : static)
     */
    public function green($green = null): int|static
    {
        if ($green !== null) {
            $this->green = $this->toChannel($green, $this->green);
            return $this;
        }
        return $this->green;
    }

    /**
     * @param float|int|string|null $blue from 0 to 255, a value out of range is clamped
     *
     * @return ($blue is null ? int : static)
     */
    public function blue($blue = null): int|static
    {
        if ($blue !== null) {
            $this->blue = $this->toChannel($blue, $this->blue);
            return $this;
        }
        return $this->blue;
    }

    /**
     * @return array{int, int, int}
     */
    public function rgbValues(): array
    {
        return [$this->red, $this->green, $this->blue];
    }

    /**
     * @return list<float|int|string>
     */
    public function values(): array
    {
        return $this->rgbValues();
    }

    /**
     * @param float|int|string $value
     * @param int              $current kept when $value is not a finite number
     *
     * @return int
     */
    protected function toChannel(float|int|string $value, int $current): int
    {
        return is_numeric($value) ? (int) round($this->limit((float) $value, 255, $current)) : $current;
    }

    /**
     * @return array{float|int, float|int, float|int, float|int, float|int}
     */
    protected function getHValues(): array
    {
        [$r, $g, $b] = $values = array_map(fn (int $value) => $value / 255, $this->rgbValues());
        [$min, $max] = [min($values), max($values)];
        return [$r, $g, $b, $min, $max];
    }

    /**
     * @return array{float|int, float|int, float|int}
     */
    protected function getHslValues(): array
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
        return [$h, $s, $l];
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
    protected function getH(float $max, float $r, float $g, float $b, float $d): float
    {
        $h = match ($max) {
            $r => ($g - $b) / $d + ($g < $b ? 6 : 0),
            $g => ($b - $r) / $d + 2,
            $b => ($r - $g) / $d + 4,
            default => $max,
        };
        return $h / 6;
    }
}
