<?php

namespace OzdemirBurak\Iris;

use OzdemirBurak\Iris\Exceptions\InvalidColorException;
use OzdemirBurak\Iris\Helpers\DefinedColor;

abstract class BaseColor implements \JsonSerializable
{
    /**
     * @param string $code
     *
     * @return bool|string
     */
    abstract protected function validate(string $code): bool|string;

    /**
     * @param string $color
     *
     * @return list<float|int|string>
     */
    abstract protected function initialize(string $color): array;

    /**
     * @return list<float|int|string>
     */
    abstract public function values(): array;

    /**
     * @return \OzdemirBurak\Iris\Color\Hexa
     */
    abstract public function toHexa(): Color\Hexa;

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
     * @return \OzdemirBurak\Iris\Color\Cmyk
     */
    abstract public function toCmyk(): Color\Cmyk;

    /**
     * @return \OzdemirBurak\Iris\Color\Rgba
     */
    abstract public function toRgba(): Color\Rgba;

    /**
     * @return \OzdemirBurak\Iris\Color\Oklch
     */
    abstract public function toOklch(): Color\Oklch;

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
        if (!is_string($color = $this->validate($code))) {
            throw new InvalidColorException($this->getExceptionMessage() . ' => ' . $code);
        }
        $this->initialize($color);
    }

    /**
     * @param float $percent
     *
     * @return static
     */
    public function saturate(float $percent): static
    {
        if ($this->isNoChange($percent)) {
            return $this->clone();
        }
        $color = $this->opaqueHsl();
        $saturation = $this->clamp(($color->saturation() + $percent) / 100);
        return $color->saturation($saturation * 100)->back($this);
    }

    /**
     * @param float $percent
     *
     * @return static
     */
    public function desaturate(float $percent): static
    {
        if ($this->isNoChange($percent)) {
            return $this->clone();
        }
        $color = $this->opaqueHsl();
        $saturation = $this->clamp(($color->saturation() - $percent) / 100);
        return $color->saturation($saturation * 100)->back($this);
    }

    /**
     * @return static
     */
    public function grayscale(): static
    {
        return $this->desaturate(100);
    }

    /**
     * @param float $percent
     *
     * @return static
     */
    public function brighten(float $percent): static
    {
        if ($this->isNoChange($percent)) {
            return $this->clone();
        }
        // The setters clamp each channel to [0, 255]
        $delta = round(255 * $percent / 100);
        $color = $this->opaqueRgb();
        $color->red($color->red() + $delta);
        $color->green($color->green() + $delta);
        $color->blue($color->blue() + $delta);
        return $color->back($this);
    }

    /**
     * @param float $percent
     *
     * @return static
     */
    public function lighten(float $percent): static
    {
        if ($this->isNoChange($percent)) {
            return $this->clone();
        }
        $color = $this->opaqueHsl();
        $lightness = $this->clamp(($color->lightness() + $percent) / 100);
        return $color->lightness($lightness * 100)->back($this);
    }

    /**
     * @param float $percent
     *
     * @return static
     */
    public function darken(float $percent): static
    {
        if ($this->isNoChange($percent)) {
            return $this->clone();
        }
        $color = $this->opaqueHsl();
        $lightness = $this->clamp(($color->lightness() - $percent) / 100);
        return $color->lightness($lightness * 100)->back($this);
    }

    /**
     * @link https://en.wikipedia.org/wiki/Luma_(video) Magic numbers taken from link
     * @return bool
     */
    public function isLight(): bool
    {
        $color = $this->toRgb();
        $darkness = 1 - (0.299 * $color->red() + 0.587 * $color->green() + 0.114 * $color->blue()) / 255;
        return $darkness < 0.5;
    }

    /**
     * @return bool
     */
    public function isDark(): bool
    {
        return !$this->isLight();
    }

    /**
     * WCAG 2 relative luminance, from 0 for black to 1 for white; a color with alpha is blended onto its background first
     *
     * @link https://www.w3.org/TR/WCAG22/#dfn-relative-luminance
     *
     * @return float
     */
    public function luminance(): float
    {
        [$r, $g, $b] = array_map(fn (int $channel) => self::linearize($channel / 255), $this->toRgb()->rgbValues());
        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /**
     * WCAG 2 contrast ratio of this color as the text on $background, from 1 to 21; a text color with alpha is
     * blended onto the background first
     *
     * @link https://www.w3.org/TR/WCAG22/#dfn-contrast-ratio
     *
     * @param \OzdemirBurak\Iris\BaseColor $background
     *
     * @return float
     */
    public function contrast(BaseColor $background): float
    {
        $back = $background->toRgb();
        $front = $this->toRgba()->background($back)->toRgb();
        [$first, $second] = [$front->luminance(), $back->luminance()];
        return (max($first, $second) + 0.05) / (min($first, $second) + 0.05);
    }

    /**
     * Whether this color as the text on $background has at least the contrast $ratio: 4.5 for normal text (WCAG AA),
     * 3 for large text, 7 for AAA
     *
     * @param \OzdemirBurak\Iris\BaseColor $background
     * @param float                        $ratio
     *
     * @return bool
     */
    public function isReadable(BaseColor $background, float $ratio = 4.5): bool
    {
        return $this->contrast($background) >= $ratio;
    }

    /**
     * The text color with the highest contrast on this color as the background; black or white when no candidates
     * are given
     *
     * @param list<\OzdemirBurak\Iris\BaseColor> $candidates
     *
     * @return \OzdemirBurak\Iris\BaseColor one of the candidates
     */
    public function mostReadable(array $candidates = []): BaseColor
    {
        $best = null;
        $bestRatio = 0.0;
        foreach ($candidates ?: [new Color\Hex('#000'), new Color\Hex('#fff')] as $candidate) {
            if (($ratio = $candidate->contrast($this)) > $bestRatio) {
                [$best, $bestRatio] = [$candidate, $ratio];
            }
        }
        return $best ?? new Color\Hex('#000');
    }

    /**
     * Color difference deltaE OK: the Euclidean distance in OKLab (CSS Color 4) between the 8-bit sRGB colors,
     * 0 for the same color; a distance below about 0.02 is hard to see. The alpha is not compared
     *
     * @link https://www.w3.org/TR/css-color-4/#color-difference-OK
     *
     * @param \OzdemirBurak\Iris\BaseColor $other
     *
     * @return float
     */
    public function distance(BaseColor $other): float
    {
        [$l1, $a1, $b1] = self::rgbToOklab(...$this->opaqueRgb()->rgbValues());
        [$l2, $a2, $b2] = self::rgbToOklab(...$other->opaqueRgb()->rgbValues());
        return sqrt(($l1 - $l2) ** 2 + ($a1 - $a2) ** 2 + ($b1 - $b2) ** 2);
    }

    /**
     * Whether $other is the same color, in any model: the distance() is not greater than $tolerance (e.g. 0.02 for
     * colors that look the same), and the alpha is the same to 2 decimals
     *
     * @param \OzdemirBurak\Iris\BaseColor $other
     * @param float                        $tolerance
     *
     * @return bool
     */
    public function equals(BaseColor $other, float $tolerance = 0.0): bool
    {
        return $this->distance($other) <= $tolerance && round($this->alphaRaw(), 2) == round($other->alphaRaw(), 2);
    }

    /**
     * The CSS name of the color, e.g. "tomato", or null when it has none; "transparent" for an alpha of 0. For the
     * colors with two names, the first name in alphabetical order, e.g. "aqua" and not "cyan"
     *
     * @return string|null
     */
    public function toName(): ?string
    {
        if ($this->alphaRaw() == 0) {
            return 'transparent';
        }
        return $this->alphaRaw() < 1 ? null : DefinedColor::name((string) $this->opaqueRgb()->toHex());
    }

    /**
     * The negative color: each RGB channel becomes 255 minus the channel; the alpha is kept
     *
     * @return static
     */
    public function invert(): static
    {
        $color = $this->opaqueRgb();
        [$red, $green, $blue] = $color->rgbValues();
        $color->red(255 - $red);
        $color->green(255 - $green);
        $color->blue(255 - $blue);
        return $color->back($this);
    }

    /**
     * The color on the other side of the color wheel, the same as spin(180)
     *
     * @return static
     */
    public function complement(): static
    {
        return $this->spin(180);
    }

    /**
     * json_encode() gives the string form, e.g. "#ff0000"
     *
     * @return string
     */
    public function jsonSerialize(): string
    {
        return (string) $this;
    }

    /**
     * @param float $percent
     *
     * @return static
     */
    public function spin(float $percent): static
    {
        if ($this->isNoChange($percent)) {
            return $this->clone();
        }
        $color = $this->opaqueHsl();
        return $color->hue($color->hue() + $percent)->back($this);
    }

    /**
     * @link https://github.com/less/less.js/blob/master/packages/less/src/less/functions/color.js
     *
     * @param \OzdemirBurak\Iris\BaseColor $color
     * @param float                        $percent weight of $color, from 0 to 100
     *
     * @return static
     */
    public function mix(BaseColor $color, float $percent = 50): static
    {
        $weight = $this->clamp($percent / 100);
        // The ends are exact: 0% gives this color, 100% gives the other color in the model of this color
        if ($this->isNoChange($weight)) {
            return $this->clone();
        }
        if ($weight == 1) {
            return $this->gradientStop($color);
        }
        $first = $this->opaqueRgb();
        $second = $color->opaqueRgb();
        // less.js and Sass: the channels of the more opaque color count more
        [$w, $a] = [2 * $weight - 1, $color->alphaRaw() - $this->alphaRaw()];
        $channelWeight = $a == 0 || $w * $a == -1 ? $weight : (1 + ($w + $a) / (1 + $w * $a)) / 2;
        $red = $first->red() * (1 - $channelWeight) + $second->red() * $channelWeight;
        $green = $first->green() * (1 - $channelWeight) + $second->green() * $channelWeight;
        $blue = $first->blue() * (1 - $channelWeight) + $second->blue() * $channelWeight;
        $alpha = $this->alphaRaw() + $a * $weight;
        return $first->red($red)->green($green)->blue($blue)->toRgba()->alpha($alpha)->back($this);
    }

    /**
     * @link https://github.com/less/less.js/blob/master/packages/less/src/less/functions/color.js
     *
     * @param float $percent
     *
     * @return static
     */
    public function tint(float $percent = 50): static
    {
        return $this->mix(new Color\Rgb('255,255,255'), $percent);
    }

    /**
     * @link https://github.com/less/less.js/blob/master/packages/less/src/less/functions/color.js
     *
     * @param float $percent
     *
     * @return static
     */
    public function shade(float $percent = 50): static
    {
        return $this->mix(new Color\Rgb('0,0,0'), $percent);
    }

    /**
     * Generate a gradient (array of colors) between this color and one or more target colors.
     *
     * @param \OzdemirBurak\Iris\BaseColor|array<\OzdemirBurak\Iris\BaseColor> $colors Target color(s) - single color or array of colors
     * @param int $steps Number of colors to generate (including start and end)
     *
     * @return list<static> Array of colors of the same type as the starting color
     */
    public function gradient(BaseColor|array $colors, int $steps = 10): array
    {
        $colorArray = is_array($colors) ? array_values($colors) : [$colors];
        if ($steps < 2 || $colorArray === []) {
            return [$this->clone()];
        }
        array_unshift($colorArray, $this);
        $totalColors = count($colorArray);
        if ($totalColors === 2) {
            return $this->generateGradientBetweenTwo($colorArray[0], $colorArray[1], $steps);
        }
        $segments = $totalColors - 1;
        // Fewer steps than colors: pick evenly spaced colors, always keeping the first and the last
        if ($steps < $totalColors) {
            return array_map(
                fn (int $i) => $this->gradientStop($colorArray[(int) round($i * $segments / ($steps - 1))]),
                range(0, $steps - 1)
            );
        }
        $stepsPerSegment = (int) floor(($steps - 1) / $segments);
        $remainder = ($steps - 1) % $segments;
        $result = [];
        for ($i = 0; $i < $segments; $i++) {
            $segmentSteps = $stepsPerSegment + ($i < $remainder ? 1 : 0) + 1;
            $segmentColors = $this->generateGradientBetweenTwo($colorArray[$i], $colorArray[$i + 1], $segmentSteps);
            if ($i > 0) {
                array_shift($segmentColors);
            }
            $result = array_merge($result, $segmentColors);
        }
        return $result;
    }

    /**
     * Generate gradient between exactly two colors.
     *
     * @param \OzdemirBurak\Iris\BaseColor $start
     * @param \OzdemirBurak\Iris\BaseColor $end
     * @param int $steps
     *
     * @return list<static>
     */
    protected function generateGradientBetweenTwo(BaseColor $start, BaseColor $end, int $steps): array
    {
        // Mix as Rgba, so that each color keeps its own alpha
        [$from, $to] = [$start->toRgba(), $end->toRgba()];
        $result = [];
        for ($i = 0; $i < $steps; $i++) {
            $result[] = match ($i) {
                0 => $this->gradientStop($start),
                $steps - 1 => $this->gradientStop($end),
                default => $from->mix($to, $i / ($steps - 1) * 100)->back($this),
            };
        }
        return $result;
    }

    /**
     * A copy of a gradient color in the model of this color, with its own alpha
     *
     * @param \OzdemirBurak\Iris\BaseColor $color
     *
     * @return static
     */
    protected function gradientStop(BaseColor $color): static
    {
        if ($color instanceof static && $this->getColorModelName($color) === $this->getColorModelName($this)) {
            return $color->clone();
        }
        return $color->toRgba()->back($this);
    }

    /**
     * @link https://github.com/less/less.js/blob/master/packages/less/src/less/functions/color.js
     *
     * @param float $percent
     *
     * @return \OzdemirBurak\Iris\Color\Hexa|\OzdemirBurak\Iris\Color\Hsla|\OzdemirBurak\Iris\Color\Oklch|\OzdemirBurak\Iris\Color\Rgba
     */
    public function fade(float $percent): Color\Hexa|Color\Hsla|Color\Oklch|Color\Rgba
    {
        return $this->toAlphaModel()->alpha($this->clamp($percent / 100));
    }

    /**
     * @param float $percent
     *
     * @return \OzdemirBurak\Iris\Color\Hexa|\OzdemirBurak\Iris\Color\Hsla|\OzdemirBurak\Iris\Color\Oklch|\OzdemirBurak\Iris\Color\Rgba
     */
    public function fadeIn(float $percent): Color\Hexa|Color\Hsla|Color\Oklch|Color\Rgba
    {
        $color = $this->toAlphaModel();
        return $color->alpha($this->clamp($color->alphaRaw() + $percent / 100));
    }

    /**
     * @param float $percent
     *
     * @return \OzdemirBurak\Iris\Color\Hexa|\OzdemirBurak\Iris\Color\Hsla|\OzdemirBurak\Iris\Color\Oklch|\OzdemirBurak\Iris\Color\Rgba
     */
    public function fadeOut(float $percent): Color\Hexa|Color\Hsla|Color\Oklch|Color\Rgba
    {
        return $this->fadeIn(-1 * $percent);
    }

    /**
     * @param float $value
     *
     * @return float
     */
    protected function clamp(float $value): float
    {
        // NAN goes through, so that the setters ignore it
        return is_nan($value) ? $value : min(1, max(0, $value));
    }

    /**
     * @param float $amount
     *
     * @return bool true when an operation with $amount gives the same color
     */
    protected function isNoChange(float $amount): bool
    {
        return $amount == 0 || is_nan($amount);
    }

    /**
     * Alpha of the color, 1.0 for the models without an alpha channel
     *
     * @return float
     */
    protected function alphaRaw(): float
    {
        return 1.0;
    }

    /**
     * Copy of the color as Rgb, without blending the alpha channel onto a background
     *
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    protected function opaqueRgb(): Color\Rgb
    {
        return $this->toRgb();
    }

    /**
     * Copy of the color as Hsl, without blending the alpha channel onto a background
     *
     * @return \OzdemirBurak\Iris\Color\Hsl
     */
    protected function opaqueHsl(): Color\Hsl
    {
        return $this->toHsl();
    }

    /**
     * Copy of the color in a model with an alpha channel: Hexa for Hex and Hexa, Hsla for Hsl and Hsla,
     * Oklch for Oklch, Rgba for all others
     *
     * @return \OzdemirBurak\Iris\Color\Hexa|\OzdemirBurak\Iris\Color\Hsla|\OzdemirBurak\Iris\Color\Oklch|\OzdemirBurak\Iris\Color\Rgba
     */
    protected function toAlphaModel(): Color\Hexa|Color\Hsla|Color\Oklch|Color\Rgba
    {
        return match ($this->getColorModelName($this)) {
            'Hex', 'Hexa' => $this->toHexa(),
            'Hsl', 'Hsla' => $this->toHsla(),
            'Oklch' => $this->toOklch(),
            default => $this->toRgba(),
        };
    }

    /**
     * $value limited to [0, $max], or $current when $value is NAN or INF
     *
     * @param float $value
     * @param float $max
     * @param float $current
     *
     * @return float
     */
    protected function limit(float $value, float $max, float $current): float
    {
        // round() removes float noise, e.g. 7.000000000000001 after saturate(0)
        return is_finite($value) ? round(max(0.0, min($max, $value)), 10) : $current;
    }

    /**
     * $hue wrapped into [0, 360), or $current when $hue is NAN or INF
     *
     * @param float $hue
     * @param float $current
     *
     * @return float
     */
    protected function wrapHue(float $hue, float $current): float
    {
        if (!is_finite($hue)) {
            return $current;
        }
        // round() removes float noise, e.g. fmod(330.9 + 30, 360) is 0.89999999999998
        $hue = round(fmod($hue, 360), 10);
        // "+ 0.0" turns -0.0 into 0.0, which would otherwise print as "-0"
        $hue = ($hue < 0 ? $hue + 360 : $hue) + 0.0;
        return $hue >= 360 ? 0.0 : $hue;
    }

    /**
     * Convert this color to the model of $color
     *
     * @template T of \OzdemirBurak\Iris\BaseColor
     *
     * @param T $color
     *
     * @return T
     */
    public function back(BaseColor $color): BaseColor
    {
        /** @var T $result */
        $result = $this->{'to' . $this->getColorModelName($color)}();
        // A model without alpha converts to alpha 1.0, so restore the alpha of the original color
        if (!method_exists($this, 'alpha') && method_exists($color, 'alpha') && method_exists($result, 'alpha')) {
            $result->alpha($color->alphaRaw());
        }
        if ($color instanceof Color\Rgba && $result instanceof Color\Rgba && $result !== $color) {
            $result->background($color->background());
        }
        return $result;
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
     * @return string
     */
    public function getColorModelName(BaseColor $color): string
    {
        return (new \ReflectionClass($color))->getShortName();
    }

    /**
     * Linearize an sRGB value from 0 to 1 (inverse gamma correction)
     *
     * @param float $value
     *
     * @return float
     */
    protected static function linearize(float $value): float
    {
        if ($value <= 0.04045) {
            return $value / 12.92;
        }
        return pow(($value + 0.055) / 1.055, 2.4);
    }

    /**
     * @link https://bottosson.github.io/posts/oklab/
     *
     * @param int $red   from 0 to 255
     * @param int $green from 0 to 255
     * @param int $blue  from 0 to 255
     *
     * @return array{float, float, float} the OKLab lightness L from 0 to 1, and a and b
     */
    protected static function rgbToOklab(int $red, int $green, int $blue): array
    {
        // Linearize sRGB, then convert linear sRGB to LMS
        [$r, $g, $b] = [self::linearize($red / 255), self::linearize($green / 255), self::linearize($blue / 255)];
        $l = 0.4122214708 * $r + 0.5363325363 * $g + 0.0514459929 * $b;
        $m = 0.2119034982 * $r + 0.6806995451 * $g + 0.1073969566 * $b;
        $s = 0.0883024619 * $r + 0.2817188376 * $g + 0.6299787005 * $b;
        // Apply cube root
        $l_ = $l >= 0 ? pow($l, 1 / 3) : -pow(-$l, 1 / 3);
        $m_ = $m >= 0 ? pow($m, 1 / 3) : -pow(-$m, 1 / 3);
        $s_ = $s >= 0 ? pow($s, 1 / 3) : -pow(-$s, 1 / 3);
        return [
            0.2104542553 * $l_ + 0.7936177850 * $m_ - 0.0040720468 * $s_,
            1.9779984951 * $l_ - 2.4285922050 * $m_ + 0.4505937099 * $s_,
            0.0259040371 * $l_ + 0.7827717662 * $m_ - 0.8086757660 * $s_,
        ];
    }

    /**
     * @return static
     */
    public function clone(): static
    {
        return clone $this;
    }
}
