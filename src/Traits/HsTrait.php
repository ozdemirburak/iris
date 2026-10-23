<?php

namespace OzdemirBurak\Iris\Traits;

use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Helpers\ColorString;
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
        $name = property_exists($this, 'lightness') ? 'hsl' : 'hsv';
        // A color name comes from its exact hex value; the hsl and hsv columns of the table are rounded
        if (($hex = DefinedColor::hex($code)) !== null) {
            $color = $name === 'hsl' ? (new Hex($hex))->toHsl() : (new Hex($hex))->toHsv();
            return implode(',', array_map('floatval', $color->getValues()));
        }
        if (($parts = ColorString::split($code, [$name], 3, false)) === false) {
            return false;
        }
        [$hue, $saturation, $third] = $parts[0];
        return ColorString::join([
            ColorString::hue($hue),
            ColorString::percentage($saturation),
            ColorString::percentage($third),
        ]);
    }

    /**
     * @param float|int|string|null $hue in degrees, wraps around 360, a value that is not a number is ignored
     *
     * @return ($hue is null ? float : static)
     */
    public function hue($hue = null): float|static
    {
        if ($hue !== null) {
            if (is_numeric($hue)) {
                $this->hue = $this->wrapHue((float) $hue, $this->hue);
            }
            return $this;
        }
        return (float) $this->hue;
    }

    /**
     * @param float|int|string|null $saturation a value out of range is clamped, a value that is not a number is ignored
     *
     * @return ($saturation is null ? float : static)
     */
    public function saturation($saturation = null): float|static
    {
        if ($saturation !== null) {
            if (is_numeric($saturation)) {
                $this->saturation = $this->limit((float) $saturation, 100, $this->saturation);
            }
            return $this;
        }
        return (float) $this->saturation;
    }

    /**
     * The third component: the lightness of HSL, the value of HSV
     *
     * @return float
     */
    abstract protected function lightnessOrValue(): float;

    /**
     * @return array{float, string, string}
     */
    public function getValues(): array
    {
        return [
            $this->hue(),
            $this->saturation() . '%',
            $this->lightnessOrValue() . '%'
        ];
    }

    /**
     * Values in [0, 1] range
     *
     * @return array{float, float, float}
     */
    public function valuesInUnitInterval(): array
    {
        return [
            $this->hue() / 360,
            $this->saturation() / 100,
            $this->lightnessOrValue() / 100
        ];
    }
}
