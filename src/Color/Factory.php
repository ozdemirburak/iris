<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Exceptions\AmbiguousColorString;
use OzdemirBurak\Iris\Exceptions\InvalidColorException;

class Factory
{
    /**
     * @param string $color
     * @return BaseColor
     * @throws AmbiguousColorString|InvalidColorException
     */
    public static function init(string $color): BaseColor
    {
        $color = str_replace(' ', '', $color);
        // Definitive types
        if (preg_match('/^(?P<type>(rgba?|hsla?|hsv))/i', $color, $match)) {
            $class = self::resolveClass($match['type']);
            return new $class($color);
        }
        // Best guess
        if (preg_match('/^#?[a-f0-9]{8}$/i', $color)) {
            return new Hexa($color);
        }
        if (preg_match('/^#?[a-f0-9]{3}([a-f0-9]{3})?$/i', $color)) {
            return new Hex($color);
        }
        if (preg_match('/^[a-z]+$/i', $color)) {
            return new Hex($color);
        }
        if (preg_match('/^\d{1,3},\d{1,3},\d{1,3}$/', $color)) {
            return new Rgb($color);
        }
        if (preg_match('/^\d{1,3},\d{1,3},\d{1,3},[0-9\.]+$/', $color)) {
            return new Rgba($color);
        }
        if (preg_match('/^\d{1,3},\d{1,3}%,\d{1,3}%,[0-9\.]+$/', $color)) {
            return new Hsla($color);
        }
        // Cannot determine between hsv and hsl
        throw new AmbiguousColorString("Cannot determine color type of '{$color}'");
    }

    /**
     * @param string $class
     * @return string
     */
    private static function resolveClass(string $class): string
    {
        return __NAMESPACE__ . '\\' . ucfirst(strtolower($class));
    }
}
