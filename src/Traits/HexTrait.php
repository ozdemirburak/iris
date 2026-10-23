<?php

namespace OzdemirBurak\Iris\Traits;

use OzdemirBurak\Iris\Color\Rgb;

/**
 * RGB channels as two hex digits, from 00 to ff
 */
trait HexTrait
{
    /**
     * @var string
     */
    protected string $red;

    /**
     * @var string
     */
    protected string $green;

    /**
     * @var string
     */
    protected string $blue;

    /**
     * @param int|string|null $red two hex digits, or one digit that is doubled; an invalid value is ignored
     *
     * @return ($red is null ? string : static)
     */
    public function red($red = null): string|static
    {
        if ($red !== null) {
            $this->red = $this->toHexChannel($red, $this->red);
            return $this;
        }
        return $this->red;
    }

    /**
     * @param int|string|null $green two hex digits, or one digit that is doubled; an invalid value is ignored
     *
     * @return ($green is null ? string : static)
     */
    public function green($green = null): string|static
    {
        if ($green !== null) {
            $this->green = $this->toHexChannel($green, $this->green);
            return $this;
        }
        return $this->green;
    }

    /**
     * @param int|string|null $blue two hex digits, or one digit that is doubled; an invalid value is ignored
     *
     * @return ($blue is null ? string : static)
     */
    public function blue($blue = null): string|static
    {
        if ($blue !== null) {
            $this->blue = $this->toHexChannel($blue, $this->blue);
            return $this;
        }
        return $this->blue;
    }

    /**
     * @return array{string, string, string}
     */
    public function rgbValues(): array
    {
        return [$this->red, $this->green, $this->blue];
    }

    /**
     * @return list<float|int|string>
     */
    public function values(): array
    {
        return $this->rgbValues();
    }

    /**
     * @param float|int|string $value
     * @param string           $current kept when $value is not valid
     *
     * @return string
     */
    protected function toHexChannel(float|int|string $value, string $current): string
    {
        $value = (string) $value;
        $value = strlen($value) === 1 ? $value . $value : $value;
        return preg_match('/^[a-f0-9]{2}$/i', $value) ? $value : $current;
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Rgb
     */
    protected function hexToRgb(): Rgb
    {
        return new Rgb(implode(',', array_map('hexdec', $this->rgbValues())));
    }
}
