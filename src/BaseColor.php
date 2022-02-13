<?php

namespace OzdemirBurak\Iris;

use OzdemirBurak\Iris\Exceptions\InvalidColorException;

abstract class BaseColor
{
    /**
     * @param string $code
     *
     * @return bool|string
     */
    abstract protected function validate(string $code);

    /**
     * @param string $color
     *
     * @return array
     */
    abstract protected function initialize(string $color): array;

    /**
     * @return array
     */
    abstract public function values(): array;

    /**
     * @return \OzdemirBurak\Iris\Color\Hex
     */
    abstract public function toHex(): Color\Hex;

    /**
     * @return \OzdemirBurak\Iris\Color\Hsl
     */
    abstract public function toHsl(): Color\Hsl;

    /**
     * @return \OzdemirBurak\Iris\Color\Hsla
     */
    abstract public function toHsla(): Color\Hsla;

    /**
     * @return \OzdemirBurak\Iris\Color\Hsv
     */
    abstract public function toHsv(): Color\Hsv;

    /**
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    abstract public function toRgb(): Color\Rgb;

    /**
     * @return \OzdemirBurak\Iris\Color\Rgba
     */
    abstract public function toRgba(): Color\Rgba;

    /**
     * @return string
     */
    abstract public function __toString(): string;

    /**
     * Color constructor.
     *
     * @param string $code
     *
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     */
    public function __construct(string $code)
    {
        if (($color = $this->validate($code)) === false) {
            throw new InvalidColorException($this->getExceptionMessage() . ' => ' . $code);
        }
        $this->initialize($color);
    }

    /**
     * @param int $percent
     *
     * @return mixed
     */
    public function saturate(int $percent)
    {
        $color = $this->toHsl();
        $saturation = $this->clamp(($color->saturation() + $percent) / 100);
        return $color->saturation($saturation * 100)->back($this);
    }

    /**
     * @param int $percent
     *
     * @return mixed
     */
    public function desaturate(int $percent)
    {
        $color = $this->toHsl();
        $saturation = $this->clamp(($color->saturation() - $percent) / 100);
        return $color->saturation($saturation * 100)->back($this);
    }

    /**
     * @return mixed
     */
    public function grayscale()
    {
        return $this->desaturate(100);
    }

    /**
     * @param int $percent
     *
     * @return mixed
     */
    public function brighten(int $percent)
    {
        $percent *= -1;
        $color = $this->toRgb();
        $color->red(max(0, min(255, $color->red() - round(255 * ($percent / 100)))));
        $color->green(max(0, min(255, $color->green() - round(255 * ($percent / 100)))));
        $color->blue(max(0, min(255, $color->blue() - round(255 * ($percent / 100)))));
        return $color->back($this);
    }

    /**
     * @param int $percent
     *
     * @return mixed
     */
    public function lighten(int $percent)
    {
        $color = $this->toHsl();
        $lightness = $this->clamp(($color->lightness() + $percent) / 100);
        return $color->lightness($lightness * 100)->back($this);
    }

    /**
     * @param int $percent
     *
     * @return mixed
     */
    public function darken(int $percent)
    {
        $color = $this->toHsl();
        $lightness = $this->clamp(($color->lightness() - $percent) / 100);
        return $color->lightness($lightness * 100)->back($this);
    }

    /**
     * @link https://en.wikipedia.org/wiki/Luma_(video) Magic numbers taken from link
     * @return boolean
     */
    public function isLight()
    {
        $color = $this->toRgb();
        $darkness = 1 - (0.299 * $color->red() + 0.587 * $color->green() + 0.114 * $color->blue()) / 255;
        return $darkness < 0.5;
    }

    /**
     * @return boolean
     */
    public function isDark()
    {
        return !$this->isLight();
    }

    /**
     * @param int $percent
     *
     * @return mixed
     */
    public function spin(int $percent)
    {
        $color = $this->toHsl();
        $hue = ($color->hue() + $percent) % 360;
        return $color->hue($hue < 0 ? 360 + $hue : $hue)->back($this);
    }

    /**
     * @param \OzdemirBurak\Iris\BaseColor $color
     * @param int                          $percent
     *
     * @return mixed
     */
    public function mix(BaseColor $color, int $percent = 50)
    {
        $first = $this->toRgb();
        $second = $color->toRgb();
        $weight = $percent / 100;
        $red = $first->red() * (1 - $weight) + $second->red() * $weight;
        $green = $first->green() * (1 - $weight) + $second->green() * $weight;
        $blue = $first->blue() * (1 - $weight) + $second->blue() * $weight;
        return $first->red($red)->green($green)->blue($blue)->back($this);
    }

    /**
     * @param \OzdemirBurak\Iris\BaseColor $color
     * @param int                          $percent
     *
     * Do a linear interpolation between the two colors in HSV space. Given
     * that the hue component is circular, there are two possible solutions;
     * the algorithm chooses the solution on the shorter arc and normalizes
     * the resulting hue to a value between 0 and 360.
     *
     * @return mixed
     */
    public function mixInHsv(BaseColor $color, int $percent = 50)
    {
        $first = $this->toHsv();
        $second = $color->toHsv();
        $weight = $percent / 100;
        $hue = $first->hue() * (1 - $weight) + $second->hue() * $weight;
        // choose hue in the middle of the shortest way between first and second
        if (abs($second->hue() - $first->hue()) > 180.) {
            $hue += 180.;
        }
        // normalize hue
        if ($hue >= 360.) {
            $hue -= 360.;
        }
        $saturation = $first->saturation() * (1 - $weight) + $second->saturation() * $weight;
        $value = $first->value() * (1 - $weight) + $second->value() * $weight;
        return $first->hue((int)($hue + .5))->saturation((int)($saturation + .5))->value((int)($value + .5))->back($this);
    }

    /**
     * @link https://github.com/less/less.js/blob/master/packages/less/src/less/functions/color.js
     *
     * @param int $percent
     *
     * @return mixed
     */
    public function tint(int $percent = 50)
    {
        $clone = clone $this;
        $white = $clone->toRgb()->red(255)->green(255)->blue(255);
        return $this->mix($white, $percent);
    }

    /**
     * @link https://github.com/less/less.js/blob/master/packages/less/src/less/functions/color.js
     *
     * @param int $percent
     *
     * @return mixed
     */
    public function shade(int $percent = 50)
    {
        $clone = clone $this;
        $black = $clone->toRgb()->red(0)->green(0)->blue(0);
        return $this->mix($black, $percent);
    }

    /**
     * @link https://github.com/less/less.js/blob/master/packages/less/src/less/functions/color.js
     *
     * @param $percent
     *
     * @return float|\OzdemirBurak\Iris\Color\Hsla|\OzdemirBurak\Iris\Color\Rgba
     */
    public function fade($percent)
    {
        [$model, $percent] = [$this->getColorModelName($this), $this->clamp($percent / 100)];
        if ($model === 'Hsl') {
            return $this->toHsla()->alpha($percent);
        }
        return $this->toRgba()->alpha($percent);
    }

    /**
     * @param $percent
     *
     * @return float|\OzdemirBurak\Iris\Color\Hsla|\OzdemirBurak\Iris\Color\Rgba
     */
    public function fadeIn($percent)
    {
        [$model, $percent] = [$this->getColorModelName($this), $percent / 100];
        if ($model === 'Hsla' || $model === 'Rgba') {
            return $this->alpha($this->clamp($this->alpha() + $percent));
        }
        if ($model === 'Hsl') {
            $hsla = $this->toHsla();
            return $hsla->alpha($this->clamp($hsla->alpha() + $percent));
        }
        $rgba = $this->toRgba();
        return $rgba->alpha($this->clamp($rgba->alpha() + $percent));
    }

    /**
     * @param $percent
     *
     * @return float|\OzdemirBurak\Iris\Color\Hsla|\OzdemirBurak\Iris\Color\Rgba
     */
    public function fadeOut($percent)
    {
        return $this->fadeIn(-1 * $percent);
    }

    /**
     * @param $value
     *
     * @return float
     */
    protected function clamp($value)
    {
        return min(1, max(0, $value));
    }

    /**
     * @param \OzdemirBurak\Iris\BaseColor $color
     *
     * @return $this|\OzdemirBurak\Iris\Color\Hex|\OzdemirBurak\Iris\Color\Hsl|\OzdemirBurak\Iris\Color\Hsv|\OzdemirBurak\Iris\Color\Rgb
     */
    public function back(BaseColor $color)
    {
        return $this->{'to' . $this->getColorModelName($color)}();
    }

    /**
     * @return string
     */
    protected function getExceptionMessage(): string
    {
        return 'Invalid ' . strtoupper(substr(static::class, strrpos(static::class, '\\') + 1)) . ' value';
    }

    /**
     * @param \OzdemirBurak\Iris\BaseColor $color
     *
     * @return false|string
     */
    public function getColorModelName(BaseColor $color)
    {
        return substr(strrchr(get_class($color), '\\'), 1);
    }
}
