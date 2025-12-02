<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;

class Oklch extends BaseColor
{
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
        $color = str_replace(['oklch', '(', ')', ' ', '%'], '', strtolower($code));
        if (preg_match('/^([\d.]+),([\d.]+),([\d.]+)$/', $color, $matches)) {
            if ($matches[1] > 100 || $matches[2] > 0.5 || $matches[3] > 360) {
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
        [$this->lightness, $this->chroma, $this->hue] = array_map('floatval', explode(',', $color));
        return $this->values();
    }

    /**
     * @return array
     */
    public function values(): array
    {
        return [
            $this->lightness(),
            $this->chroma(),
            $this->hue()
        ];
    }

    /**
     * @param float|null $lightness
     *
     * @return float|self
     */
    public function lightness(?float $lightness = null): float|self
    {
        if ($lightness !== null) {
            $this->lightness = max(0, min(100, $lightness));
            return $this;
        }
        return round($this->lightness, 2);
    }

    /**
     * @param float|null $chroma
     *
     * @return float|self
     */
    public function chroma(?float $chroma = null): float|self
    {
        if ($chroma !== null) {
            $this->chroma = max(0, min(0.5, $chroma));
            return $this;
        }
        return round($this->chroma, 4);
    }

    /**
     * @param float|null $hue
     *
     * @return float|self
     */
    public function hue(?float $hue = null): float|self
    {
        if ($hue !== null) {
            $this->hue = fmod(fmod($hue, 360) + 360, 360);
            return $this;
        }
        return round($this->hue, 2);
    }

    /**
     * Convert OKLCH to RGB
     *
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    public function toRgb(): Rgb
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
        return $this->toHex()->toHexa();
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
        return $this->toHsl()->toHsla();
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
        return $this->toRgb()->toRgba();
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
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'oklch(' . $this->lightness() . '% ' . $this->chroma() . ' ' . $this->hue() . ')';
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
        // Normalize RGB to 0-1
        $r = $r / 255;
        $g = $g / 255;
        $b = $b / 255;

        // Apply inverse gamma correction (linearize sRGB)
        $r = self::linearize($r);
        $g = self::linearize($g);
        $b = self::linearize($b);

        // Convert linear sRGB to LMS
        $l = 0.4122214708 * $r + 0.5363325363 * $g + 0.0514459929 * $b;
        $m = 0.2119034982 * $r + 0.6806995451 * $g + 0.1073969566 * $b;
        $s = 0.0883024619 * $r + 0.2817188376 * $g + 0.6299787005 * $b;

        // Apply cube root
        $l_ = $l >= 0 ? pow($l, 1/3) : -pow(-$l, 1/3);
        $m_ = $m >= 0 ? pow($m, 1/3) : -pow(-$m, 1/3);
        $s_ = $s >= 0 ? pow($s, 1/3) : -pow(-$s, 1/3);

        // Convert to Oklab
        $L = 0.2104542553 * $l_ + 0.7936177850 * $m_ - 0.0040720468 * $s_;
        $a = 1.9779984951 * $l_ - 2.4285922050 * $m_ + 0.4505937099 * $s_;
        $bVal = 0.0259040371 * $l_ + 0.7827717662 * $m_ - 0.8086757660 * $s_;

        // Convert Oklab to OKLCH
        $C = sqrt($a * $a + $bVal * $bVal);
        $h = atan2($bVal, $a) * 180 / M_PI;
        if ($h < 0) {
            $h += 360;
        }

        $lightness = round($L * 100, 2);
        $chroma = round($C, 4);
        $hue = round($h, 2);

        return new self("{$lightness},{$chroma},{$hue}");
    }

    /**
     * Linearize sRGB value (inverse gamma correction)
     *
     * @param float $value
     * @return float
     */
    protected static function linearize(float $value): float
    {
        if ($value <= 0.04045) {
            return $value / 12.92;
        }
        return pow(($value + 0.055) / 1.055, 2.4);
    }
}
