<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Helpers\DefinedColor;
use OzdemirBurak\Iris\Traits\AlphaTrait;
use OzdemirBurak\Iris\Traits\HslTrait;

class Hsla extends BaseColor
{
    use AlphaTrait, HslTrait;

    /**
     * @param string $code
     *
     * @return false|string
     */
    protected function validate(string $code)
    {
        [$class, $index] = property_exists($this, 'lightness') ? ['hsl', 2] : ['hsv', 3];
        $color = str_replace(["{$class}a", '(', ')', ' ', '%'], '', DefinedColor::find($code, $index));
        if (substr_count($color, ',') === 2) {
            $color = "{$color},1.0";
        }
        $color = $this->fixPrecision($color);
        if (preg_match($this->validationRules(), $color, $matches)) {
            if ($matches[1] > 360 || $matches[2] > 100 || $matches[3] > 100 || $matches[4] > 1) {
                return false;
            }
            return $color;
        }
        return false;
    }

    /**
     * @param string $color
     *
     * @return array
     */
    protected function initialize(string $color): array
    {
        [$this->hue, $this->saturation, $this->lightness, $this->alpha] = explode(',', $color);
        $this->alpha = (double) $this->alpha;
        return $this->values();
    }

    /**
     * @return array
     */
    public function values(): array
    {
        return array_merge($this->getValues(), [$this->alpha()]);
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsl
     */
    public function toHsl(): Hsl
    {
        return $this->toRgba()->toHsl();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgba
     */
    public function toRgba(): Rgba
    {
        return $this->convertToRgb()->toRgba()->alpha($this->alpha());
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    public function toRgb(): Rgb
    {
        return $this->toRgba()->toRgb();
    }

    /**
     * @return \OzdemirBurak\Iris\Color\Hsla
     */
    public function toHsla(): Hsla
    {
        return $this;
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsv
     */
    public function toHsv(): Hsv
    {
        return $this->toRgba()->toHsv();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hex
     */
    public function toHex(): Hex
    {
        return $this->toRgba()->toHex();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hexa
     */
    public function toHexa(): Hexa
    {
        return $this->toHex()->toHexa()->alpha($this->alpha());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'hsla(' . implode(',', $this->values()) . ')';
    }
}
