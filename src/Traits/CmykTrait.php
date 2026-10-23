<?php

namespace OzdemirBurak\Iris\Traits;

trait CmykTrait
{
    /**
     * @var float
     */
    public float $cyan;

    /**
     * @var float
     */
    public float $magenta;

    /**
     * @var float
     */
    public float $yellow;

    /**
     * @var float
     */
    public float $black;

    /**
     * @param float|int|string|null $cyan a value out of range is clamped, a value that is not a number is ignored
     *
     * @return ($cyan is null ? float : static)
     */
    public function cyan($cyan = null): float|static
    {
        if ($cyan !== null) {
            if (is_numeric($cyan)) {
                $this->cyan = $this->limit((float) $cyan, 100, $this->cyan);
            }
            return $this;
        }
        return (float) $this->cyan;
    }

    /**
     * @param float|int|string|null $magenta a value out of range is clamped, a value that is not a number is ignored
     *
     * @return ($magenta is null ? float : static)
     */
    public function magenta($magenta = null): float|static
    {
        if ($magenta !== null) {
            if (is_numeric($magenta)) {
                $this->magenta = $this->limit((float) $magenta, 100, $this->magenta);
            }
            return $this;
        }
        return (float) $this->magenta;
    }

    /**
     * @param float|int|string|null $yellow a value out of range is clamped, a value that is not a number is ignored
     *
     * @return ($yellow is null ? float : static)
     */
    public function yellow($yellow = null): float|static
    {
        if ($yellow !== null) {
            if (is_numeric($yellow)) {
                $this->yellow = $this->limit((float) $yellow, 100, $this->yellow);
            }
            return $this;
        }
        return (float) $this->yellow;
    }

    /**
     * @param float|int|string|null $black a value out of range is clamped, a value that is not a number is ignored
     *
     * @return ($black is null ? float : static)
     */
    public function black($black = null): float|static
    {
        if ($black !== null) {
            if (is_numeric($black)) {
                $this->black = $this->limit((float) $black, 100, $this->black);
            }
            return $this;
        }
        return (float) $this->black;
    }

    /**
     * @return array{float, float, float, float}
     */
    public function values(): array
    {
        return [
            $this->cyan(),
            $this->magenta(),
            $this->yellow(),
            $this->black()
        ];
    }
}
