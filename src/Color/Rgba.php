<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Helpers\ColorString;
use OzdemirBurak\Iris\Helpers\DefinedColor;
use OzdemirBurak\Iris\Traits\AlphaTrait;
use OzdemirBurak\Iris\Traits\RgbTrait;

class Rgba extends BaseColor
{
    use AlphaTrait, RgbTrait;

    /**
     * @var \OzdemirBurak\Iris\Color\Rgb
     */
    protected Rgb $background;

    /**
     * The default background, created only once
     *
     * @var \OzdemirBurak\Iris\Color\Rgb|null
     */
    private static ?Rgb $white = null;

    /**
     * @param string $code
     *
     * @return bool|string
     */
    protected function validate(string $code): bool|string
    {
        if (ColorString::isTransparent($code)) {
            return '0,0,0,0';
        }
        if (($parts = ColorString::split(DefinedColor::find($code, 1), ['rgba', 'rgb'], 3, true)) === false) {
            return false;
        }
        [$channels, $alpha] = $parts;
        return ColorString::join([...array_map([ColorString::class, 'channel'], $channels), ColorString::alpha($alpha)]);
    }

    /**
     * @param string $color
     *
     * @return list<float|int>
     */
    protected function initialize(string $color): array
    {
        $colors = explode(',', $color);
        [$this->red, $this->green, $this->blue] = array_map('intval', $colors);
        $this->alpha = (float) $colors[3];
        $this->background = $this->defaultBackground();
        return $this->values();
    }

    /**
     * @return array{int, int, int, float}
     */
    public function values(): array
    {
        return [
            $this->red(),
            $this->green(),
            $this->blue(),
            $this->alpha()
        ];
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    public function toRgb(): Rgb
    {
        [$red, $green, $blue] = array_map(function ($attribute) {
            $value = (1 - $this->alpha) * $this->background->{$attribute}() + $this->alpha * $this->{$attribute}();
            // Round away float noise first, so that e.g. 2.9999999999 does not floor to 2
            return round($value);
        }, ['red', 'green', 'blue']);
        return new Rgb(implode(',', [$red, $green, $blue]));
    }

    /**
     * @return \OzdemirBurak\Iris\Color\Rgba
     */
    public function toRgba(): Rgba
    {
        return clone $this;
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hex
     */
    public function toHex(): Hex
    {
        return $this->toRgb()->toHex();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hexa
     */
    public function toHexa(): Hexa
    {
        $hex = sprintf('%02x%02x%02x', $this->red(), $this->green(), $this->blue());
        $hexa = new Hexa($hex . 'ff');
        $hexa->alpha($this->alphaRaw());
        return $hexa;
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsl
     */
    public function toHsl(): Hsl
    {
        return $this->toRgb()->toHsl();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsla
     */
    public function toHsla(): Hsla
    {
        [$h, $s, $l] = $this->getHslValues();
        [$h, $s, $l] = [round($h * 360, 1), round($s * 100, 1), round($l * 100, 1)];
        $hsla = new Hsla("{$h},{$s}%,{$l}%,1.0");
        $hsla->alpha($this->alpha);
        return $hsla;
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsv
     */
    public function toHsv(): Hsv
    {
        return $this->toRgb()->toHsv();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Cmyk
     */
    public function toCmyk(): Cmyk
    {
        return $this->toRgb()->toCmyk();
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Oklch
     */
    public function toOklch(): Oklch
    {
        $oklch = $this->opaqueRgb()->toOklch();
        $oklch->alpha($this->alpha);
        return $oklch;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'rgba(' . implode(',', $this->values()) . ')';
    }

    /**
     * @param \OzdemirBurak\Iris\Color\Rgb|null $rgb the background to blend onto, null to read it
     *
     * @return ($rgb is null ? \OzdemirBurak\Iris\Color\Rgb : static)
     */
    public function background(?Rgb $rgb = null): Rgb|static
    {
        // Copies, so that a later change to the given or the returned object does not change this color
        if ($rgb === null) {
            return clone $this->background;
        }
        $this->background = clone $rgb;
        return $this;
    }

    /**
     * A clone must not share the background object
     */
    public function __clone()
    {
        $this->background = clone $this->background;
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    protected function opaqueRgb(): Rgb
    {
        return new Rgb(implode(',', $this->rgbValues()));
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hsl
     */
    protected function opaqueHsl(): Hsl
    {
        return $this->opaqueRgb()->toHsl();
    }

    /**
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    protected function defaultBackground(): Rgb
    {
        return clone (self::$white ??= new Rgb('255,255,255'));
    }
}
