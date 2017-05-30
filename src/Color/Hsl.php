<?php

namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Traits\HsTrait;

class Hsl extends BaseColor
{
    use HsTrait;

    /**
     * @var string
     */
    protected $exceptionMessage = 'Invalid HSL value.';

    /**
     * @var int
     */
    protected $lightness;

    /**
     * @param string $color
     *
     * @return array
     */
    protected function initialize($color)
    {
        list($this->hue, $this->saturation, $this->lightness) = explode(',', $color);
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \OzdemirBurak\Iris\Color\Hex
     */
    public function toHex()
    {
        return $this->toRgb()->toHex();
    }

    /**
     * @return \Ozdemirburak\Iris\Color\Hsl
     */
    public function toHsl()
    {
        return $this;
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \Ozdemirburak\Iris\Color\Hsv
     */
    public function toHsv()
    {
        list($h, $s, $l) = $this->valuesInUnitInterval();
        $t = $s * $l < 0.5 ? $l : 1 - $l;
        $s = 2 * $t / ($l + $t);
        $l += $t;
        $code = implode(',', [round($h * 360), round($s * 100), round($l * 100)]);
        return new Hsv($code);
    }

    /**
     * @throws \OzdemirBurak\Iris\Exceptions\InvalidColorException
     * @return \Ozdemirburak\Iris\Color\Rgb
     */
    public function toRgb()
    {
        list($h, $s, $l) = $this->valuesInUnitInterval();
        if ($s === 0) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) :
                $l + $s - $l * $s;
            $p = 2 * $l - $q;
            list($r, $g, $b) = [
                $this->hueToRgb($p, $q, $h + 1/3),
                $this->hueToRgb($p, $q, $h),
                $this->hueToRgb($p, $q, $h - 1/3)
            ];
        }
        $code = implode(',', [round($r * 255), round($g * 255), round($b * 255)]);
        return new Rgb($code);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'hsl(' . implode(',', $this->values()) . ')';
    }

    /**
     * @param int|string $lightness
     *
     * @return int|$this
     */
    public function lightness($lightness = null)
    {
        if (is_numeric($lightness)) {
            $this->lightness = $lightness >= 0 && $lightness <= 100 ? $lightness : $this->lightness;
            return $this;
        }
        return (int) $this->lightness;
    }

    /**
     * @param float $p
     * @param float $q
     * @param float $t
     *
     * @return mixed
     */
    private function hueToRgb($p, $q, $t)
    {
        if ($t < 0) {
            $t++;
        }
        if ($t > 1) {
            $t--;
        }
        if ($t < 1/6) {
            return $p + ($q - $p) * 6 * $t;
        }
        if ($t < 1/2) {
            return $q;
        }
        if ($t < 2/3) {
            return $p + ($q - $p) * (2/3 - $t) * 6;
        }
        return $p;
    }
}
