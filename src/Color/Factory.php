<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Exceptions\AmbiguousColorString;
use OzdemirBurak\Iris\Exceptions\InvalidColorException;
use OzdemirBurak\Iris\Helpers\ColorString;

class Factory
{
    /**
     * @param string $color
     * @return BaseColor
     * @throws AmbiguousColorString|InvalidColorException
     */
    public static function init(string $color): BaseColor
    {
        if (($color = trim($color, ColorString::WHITESPACE)) === '') {
            throw new InvalidColorException('Empty color value');
        }
        // Definitive types: the classes parse their own whitespace, which oklch needs as a separator
        if (preg_match('/^(?P<type>rgba?|hsla?|hsv|cmyk|oklch)/i', $color, $match)) {
            $type = strtolower($match['type']);
            // CSS Color 4: rgb() and hsl() also take an alpha
            if (in_array($type, ['rgb', 'hsl'], true) && ($parts = ColorString::split($color, [$type], 3, true)) && $parts[1] !== null) {
                $type .= 'a';
            }
            return match ($type) {
                'rgb' => new Rgb($color),
                'rgba' => new Rgba($color),
                'hsl' => new Hsl($color),
                'hsla' => new Hsla($color),
                'hsv' => new Hsv($color),
                'cmyk' => new Cmyk($color),
                default => new Oklch($color),
            };
        }
        if (ColorString::isTransparent($color)) {
            return new Rgba($color);
        }
        // "10 20 30" is "10,20,30", the values must not merge into the hex "102030"
        $code = (string) preg_replace('/\s*,\s*|\s+/', ',', $color);
        // Best guess: a 4th value of 0 or 1 is an RGBA alpha, other integers are a CMYK black
        if (preg_match('/^\d{1,3},\d{1,3},\d{1,3},(\d{2,3}|[2-9])$/', $code)) {
            return new Cmyk($code);
        }
        // A 4-digit hex needs a "#", so that words like "cafe" are not read as colors
        if (preg_match('/^(#[a-f0-9]{4}|#?[a-f0-9]{8})$/i', $code)) {
            return new Hexa($code);
        }
        if (preg_match('/^#?[a-f0-9]{3}([a-f0-9]{3})?$/i', $code)) {
            return new Hex($code);
        }
        if (preg_match('/^[a-z]+$/i', $code)) {
            return new Hex($code);
        }
        if (preg_match('/^\d{1,3},\d{1,3},\d{1,3}$/', $code)) {
            return new Rgb($code);
        }
        if (preg_match('/^\d{1,3},\d{1,3},\d{1,3},[0-9\.]+$/', $code)) {
            return new Rgba($code);
        }
        if (preg_match('/^\d{1,3},\d{1,3}%,\d{1,3}%,[0-9\.]+$/', $code)) {
            return new Hsla($code);
        }
        if (preg_match('/^[a-z-]+\(/i', $color)) {
            throw new InvalidColorException("Unsupported color function => {$color}");
        }
        // Cannot determine between hsv and hsl
        throw new AmbiguousColorString("Cannot determine color type of '{$color}'");
    }

    /**
     * Like init(), but returns null for a string that is not a color or that is ambiguous
     *
     * @param string $color
     *
     * @return \OzdemirBurak\Iris\BaseColor|null
     */
    public static function tryInit(string $color): ?BaseColor
    {
        try {
            return self::init($color);
        } catch (AmbiguousColorString|InvalidColorException) {
            return null;
        }
    }

    /**
     * @param string $color
     *
     * @return bool whether init() can read $color
     */
    public static function isValid(string $color): bool
    {
        return self::tryInit($color) !== null;
    }
}
