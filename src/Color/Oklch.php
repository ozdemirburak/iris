<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Helpers\ColorString;
use OzdemirBurak\Iris\Traits\AlphaTrait;

class Oklch extends BaseColor
{
    use AlphaTrait;

    protected float $lightness;
    protected float $chroma;
    protected float $hue;

    /**
     * @param string $code
     *
     * @return string|bool
     */
    protected function validate(string $code): bool|string
    {
        if (($parts = ColorString::split($code, ['oklch'], 3, true)) === false || ($alpha = ColorString::alpha($parts[1])) === false) {
            return false;
        }
        [$lightness, $chroma, $hue] = $parts[0];
        $lightness = ColorString::number($lightness, ['', '%']);
        $chroma = ColorString::number($chroma, ['', '%']);
        if ($lightness === false || $chroma === false || ($hue = ColorString::hue($hue)) === false) {
            return false;
        }
        // CSS Color 4: a lightness without "%" is a number from 0 to 1, and a chroma of 100% is 0.4
        $lightness = $lightness[1] === '' && $lightness[0] <= 1 ? $lightness[0] * 100 : $lightness[0];
        $chroma = $chroma[1] === '%' ? $chroma[0] * 0.004 : $chroma[0];
        return $lightness <= 100 && $chroma <= 0.5 ? "{$lightness},{$chroma},{$hue},{$alpha}" : false;
    }

    /**
     * @param string $color
     *
     * @return array{float, float, float, float}
     */
    protected function initialize(string $color): array
    {
        [$this->lightness, $this->chroma, $this->hue, $this->alpha] = array_map('floatval', explode(',', $color));
        return $this->values();
    }

    /**
     * @return array{float, float, float, float}
     */
    public function values(): array
    {
        return [
            $this->lightness(),
            $this->chroma(),
            $this->hue(),
            $this->alpha()
        ];
    }

    /**
     * @param float|null $lightness from 0 to 100, a value out of range is clamped
     *
     * @return ($lightness is null ? float : static)
     */
    public function lightness(?float $lightness = null): float|static
    {
        if ($lightness !== null) {
            $this->lightness = $this->limit($lightness, 100, $this->lightness);
            return $this;
        }
        return round($this->lightness, 2);
    }

    /**
     * @param float|null $chroma from 0 to 0.5, a value out of range is clamped
     *
     * @return ($chroma is null ? float : static)
     */
    public function chroma(?float $chroma = null): float|static
    {
        if ($chroma !== null) {
            $this->chroma = $this->limit($chroma, 0.5, $this->chroma);
            return $this;
        }
        return round($this->chroma, 4);
    }

    /**
     * @param float|null $hue in degrees, wraps around 360
     *
     * @return ($hue is null ? float : static)
     */
    public function hue(?float $hue = null): float|static
    {
        if ($hue !== null) {
            $this->hue = $this->wrapHue($hue, $this->hue);
            return $this;
        }
        // A hue of 359.996 rounds to 360, which is 0
        $hue = round($this->hue, 2);
        return $hue >= 360 ? 0.0 : $hue;
    }

    /**
     * Convert OKLCH to RGB, blended onto a white background when the alpha is below 1
     *
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    public function toRgb(): Rgb
    {
        return $this->toRgba()->toRgb();
    }

    /**
     * Convert OKLCH to RGB, without the alpha
     *
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    protected function opaqueRgb(): Rgb
    {
        // Convert OKLCH to Oklab
        $L = $this->lightness / 100;
        $C = $this->chroma;
        $h = $this->hue * M_PI / 180;

        $a = $C * cos($h);
        $b = $C * sin($h);

        // Convert Oklab to linear RGB
        $l_ = $L + 0.3963377774 * $a + 0.2158037573 * $b;
        $m_ = $L - 0.1055613458 * $a - 0.0638541728 * $b;
        $s_ = $L - 0.0894841775 * $a - 1.2914855480 * $b;

        $l = $l_ * $l_ * $l_;
        $m = $m_ * $m_ * $m_;
        $s = $s_ * $s_ * $s_;

        // Convert LMS to linear sRGB
        $r = +4.0767416621 * $l - 3.3077115913 * $m + 0.2309699292 * $s;
        $g = -1.2684380046 * $l + 2.6097574011 * $m - 0.3413193965 * $s;
        $bl = -0.0041960863 * $l - 0.7034186147 * $m + 1.7076147010 * $s;

        // Convert linear RGB to sRGB (gamma correction)
        $r = $this->gammaCorrect($r);
        $g = $this->gammaCorrect($g);
        $bl = $this->gammaCorrect($bl);

        // Clamp and convert to 0-255 range
        $r = (int) round(max(0, min(1, $r)) * 255);
        $g = (int) round(max(0, min(1, $g)) * 255);
        $bl = (int) round(max(0, min(1, $bl)) * 255);

        return new Rgb("{$r},{$g},{$bl}");
    }

    /**
     * Apply gamma correction for sRGB
     *
     * @param float $value
     * @return float
     */
    protected function gammaCorrect(float $value): float
    {
        if ($value <= 0.0031308) {
            return 12.92 * $value;
        }
        return 1.055 * pow($value, 1 / 2.4) - 0.055;
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
        return $this->toRgba()->toHexa();
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
        return $this->toRgba()->toHsla();
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
     * @return \OzdemirBurak\Iris\Color\Rgba
     */
    public function toRgba(): Rgba
    {
        $rgba = $this->opaqueRgb()->toRgba();
        $rgba->alpha($this->alpha);
        return $rgba;
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
     * @return \OzdemirBurak\Iris\Color\Oklch
     */
    public function toOklch(): Oklch
    {
        return clone $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        // As in CSS, the alpha is only written when it is below 1
        $alpha = $this->alpha() < 1 ? ' / ' . $this->alpha() : '';
        return 'oklch(' . $this->lightness() . '% ' . $this->chroma() . ' ' . $this->hue() . $alpha . ')';
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
     * Convert RGB to OKLCH (static factory method)
     *
     * @param int $r Red (0-255)
     * @param int $g Green (0-255)
     * @param int $b Blue (0-255)
     * @return self
     */
    public static function fromRgb(int $r, int $g, int $b): self
    {
        [$L, $a, $bVal] = self::rgbToOklab($r, $g, $b);

        // Convert Oklab to OKLCH
        $C = sqrt($a * $a + $bVal * $bVal);
        $h = atan2($bVal, $a) * 180 / M_PI;
        if ($h < 0) {
            $h += 360;
        }

        // Not rounded, so that a conversion back to RGB gives the same color; the getters round
        $oklch = new self('0% 0 0');
        $oklch->lightness($L * 100);
        $oklch->chroma($C);
        // The hue of an achromatic color (gray, white, black) is only float noise
        $oklch->hue(round($C, 4) == 0 ? 0 : $h);
        return $oklch;
    }
}
