<?php

namespace OzdemirBurak\Iris\Traits;

use OzdemirBurak\Iris\Helpers\DefinedColor;

trait HsTrait
{
    /**
     * @var float
     */
    protected float $hue;

    /**
     * @var float
     */
    protected float $saturation;

    /**
     * @param string $code
     *
     * @return string|bool
     */
    protected function validate(string $code): bool|string
    {
        [$class, $index] = property_exists($this, 'lightness') ? ['hsl', 2] : ['hsv', 3];
        $color = str_replace([$class, '(', ')', ' ', '%'], '', DefinedColor::find($code, $index));
        if (preg_match($this->validationRules(), $color, $matches)) {
            if ($matches[1] > 360 || $matches[2] > 100 || $matches[3] > 100) {
                return false;
            }
            return $color;
        }
        return false;
    }

    /**
     * @return string
     */
    protected function validationRules(): string
    {
        if (property_exists($this, 'alpha')) {
            return '/^(\d{1,3}(?:\.\d+)?),(\d{1,3}%?(?:\.\d+)?),(\d{1,3}%?(?:\.\d+)?),(\d+(?:\.\d+)?)$/';
        }
        return '/^(\d{1,3}(?:\.\d+)?),(\d{1,3}%?(?:\.\d+)?),(\d{1,3}%?(?:\.\d+)?)$/';
    }

    /**
     * @param float|string $hue
     *
     * @return float|$this
     */
    public function hue($hue = null): float|static
    {
        if (is_numeric($hue)) {
            $this->hue = $hue >= 0 && $hue <= 360 ? $hue : $this->hue;
            return $this;
        }
        return (float) $this->hue;
    }

    /**
     * @param float|string $saturation
     *
     * @return float|$this
     */
    public function saturation($saturation = null): float|static
    {
        if (is_numeric($saturation)) {
            $this->saturation = $saturation >= 0 && $saturation <= 100 ? $saturation : $this->saturation;
            return $this;
        }
        return (float) $this->saturation;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return [
            $this->hue(),
            $this->saturation() . '%',
            (property_exists($this, 'lightness') ? $this->lightness() : $this->value()) . '%'
        ];
    }

    /**
     * Values in [0, 1] range
     *
     * @return array
     */
    public function valuesInUnitInterval(): array
    {
        return [
            $this->hue() / 360,
            $this->saturation() / 100,
            (property_exists($this, 'lightness') ? $this->lightness() : $this->value()) / 100
        ];
    }
}
