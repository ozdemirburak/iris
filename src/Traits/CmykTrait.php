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
     * @param float|string $cyan
     *
     * @return float|$this
     */
    public function cyan($cyan = null): float|static
    {
        if (is_numeric($cyan)) {
            $this->cyan = $cyan >= 0 && $cyan <= 100 ? $cyan : $this->cyan;
            return $this;
        }
        return (float) $this->cyan;
    }

    /**
     * @param float|string $magenta
     *
     * @return float|$this
     */
    public function magenta($magenta = null): float|static
    {
        if (is_numeric($magenta)) {
            $this->magenta = $magenta >= 0 && $magenta <= 100 ? $magenta : $this->magenta;
            return $this;
        }
        return (float) $this->magenta;
    }

    /**
     * @param float|string $yellow
     *
     * @return float|$this
     */
    public function yellow($yellow = null): float|static
    {
        if (is_numeric($yellow)) {
            $this->yellow = $yellow >= 0 && $yellow <= 100 ? $yellow : $this->yellow;
            return $this;
        }
        return (float) $this->yellow;
    }

    /**
     * @param float|string $black
     *
     * @return float|$this
     */
    public function black($black = null): float|static
    {
        if (is_numeric($black)) {
            $this->black = $black >= 0 && $black <= 100 ? $black : $this->black;
            return $this;
        }
        return (float) $this->black;
    }

    /**
     * @return array
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

    /**
     * @param string           $property
     * @param float|int|string $value
     */
    protected function validateAndSet(string $property, float|int|string $value): void
    {
        if (!empty($this->castsInteger)) {
            $this->{$property} = $value >= 0 && $value <= 100 ? (int) round($value) : $this->{$property};
        } else {
            $value = strlen($value) === 1 ? $value . $value : $value;
            $this->{$property} = preg_match('/^[a-f0-9]{2}$/i', $value) ? $value : $this->{$property};
        }
    }
}