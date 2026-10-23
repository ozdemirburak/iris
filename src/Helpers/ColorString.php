<?php

namespace OzdemirBurak\Iris\Helpers;

/**
 * Parses the components of CSS color functions, in both the legacy comma syntax, e.g. "rgba(10, 20, 30, 0.5)",
 * and the CSS Color 4 space syntax, e.g. "rgb(10 20 30 / 50%)".
 */
class ColorString
{
    /**
     * The characters that trim() removes around a color, CSS whitespace included ("\f")
     */
    public const WHITESPACE = " \n\r\t\v\f\0";

    /**
     * Split a color function into its components. The function name and the parentheses are optional,
     * the components are separated by commas or whitespace, and the alpha follows a "/" or is the last component.
     *
     * @param string   $code
     * @param string[] $names    accepted function names, longest first, e.g. ['rgba', 'rgb']
     * @param int      $count    number of components, without the alpha
     * @param bool     $hasAlpha whether an alpha component is accepted
     *
     * @return array{0: list<string>, 1: string|null}|false the components, and the alpha or null when there is none
     */
    public static function split(string $code, array $names, int $count, bool $hasAlpha): array|false
    {
        $code = strtolower(trim($code, self::WHITESPACE));
        // Remove the closing parenthesis here, a lazy "(.*?)\)?$" in the regex can exceed the backtrack limit
        if (str_ends_with($code, ')')) {
            $code = substr($code, 0, -1);
        }
        $names = implode('|', array_map(fn (string $name) => preg_quote($name, '/'), $names));
        if (!preg_match("/^(?:{$names})?\\s*\\(?(.*)$/s", $code, $matches)) {
            return false;
        }
        $parts = explode('/', $matches[1]);
        if (count($parts) > 2) {
            return false;
        }
        if (($components = preg_split('/\s*,\s*|\s+/', trim($parts[0]))) === false) {
            return false;
        }
        $alpha = isset($parts[1]) ? trim($parts[1]) : null;
        if ($alpha === null && count($components) === $count + 1) {
            $alpha = array_pop($components);
        }
        if (count($components) !== $count || ($alpha !== null && !$hasAlpha)) {
            return false;
        }
        return [$components, $alpha];
    }

    /**
     * @param string   $component e.g. "50", "+50%", ".5", "1e-5", "-120deg" or "none"
     * @param string[] $units     accepted units, '' for none
     *
     * @return array{0: float, 1: string}|false the number and its unit
     */
    public static function number(string $component, array $units = ['']): array|false
    {
        if ($component === 'none') {
            return [0.0, ''];
        }
        // CSS numbers can have a sign and an exponent, and PHP prints very small floats with one, e.g. 1.0E-5
        $pattern = '/^([+-]?(?:\d+(?:\.\d+)?|\.\d+)(?:e[+-]?\d+)?)([a-z%]*)$/';
        if (!preg_match($pattern, $component, $matches) || !in_array($matches[2], $units, true)) {
            return false;
        }
        $value = (float) $matches[1];
        return is_finite($value) ? [$value + 0.0, $matches[2]] : false;
    }

    /**
     * RGB channel from 0 to 255, a percentage is relative to 255, decimals are rounded
     *
     * @param string $component
     *
     * @return int|false
     */
    public static function channel(string $component): int|false
    {
        if (($number = static::number($component, ['', '%'])) === false) {
            return false;
        }
        // "* 255 / 100" and not "* 2.55": 50 * 2.55 is 127.49999999999999 in floating point
        $value = round($number[1] === '%' ? $number[0] * 255 / 100 : $number[0]);
        return $value >= 0 && $value <= 255 ? (int) $value : false;
    }

    /**
     * Saturation, lightness, value or a CMYK component, from 0 to 100, with or without "%"
     *
     * @param string $component
     *
     * @return float|false
     */
    public static function percentage(string $component): float|false
    {
        if (($number = static::number($component, ['', '%'])) === false) {
            return false;
        }
        return $number[0] >= 0 && $number[0] <= 100 ? $number[0] : false;
    }

    /**
     * Hue in degrees, wrapped into [0, 360) like in CSS; the units deg, grad, rad and turn are converted
     *
     * @param string $component
     *
     * @return float|false
     */
    public static function hue(string $component): float|false
    {
        if (($number = static::number($component, ['', 'deg', 'grad', 'rad', 'turn'])) === false) {
            return false;
        }
        $degrees = match ($number[1]) {
            'grad' => $number[0] * 0.9,
            'rad' => rad2deg($number[0]),
            'turn' => $number[0] * 360,
            default => $number[0],
        };
        // round() removes float noise; "+ 0.0" turns -0.0 into 0.0
        $degrees = round(fmod($degrees, 360), 10);
        $degrees = ($degrees < 0 ? $degrees + 360 : $degrees) + 0.0;
        return $degrees >= 360 ? 0.0 : $degrees;
    }

    /**
     * Alpha from 0 to 1, a percentage is relative to 1, a missing alpha is 1
     *
     * @param string|null $component
     *
     * @return float|false
     */
    public static function alpha(?string $component): float|false
    {
        if ($component === null) {
            return 1.0;
        }
        if (($number = static::number($component, ['', '%'])) === false) {
            return false;
        }
        $value = $number[1] === '%' ? $number[0] / 100 : $number[0];
        return $value >= 0 && $value <= 1 ? $value : false;
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    public static function isTransparent(string $code): bool
    {
        return strtolower(trim($code, self::WHITESPACE)) === 'transparent';
    }

    /**
     * Join parsed values into the comma-separated form that the color classes store
     *
     * @param array<int|float|false> $values
     *
     * @return string|false false when any value is invalid
     */
    public static function join(array $values): string|false
    {
        return in_array(false, $values, true) ? false : implode(',', $values);
    }
}
