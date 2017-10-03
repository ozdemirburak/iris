<?php


namespace OzdemirBurak\Iris\Color;

use OzdemirBurak\Iris\Helpers\DefinedColor;

class Hsla extends Hsl
{
    protected $exceptionMessage = 'Invalid HSLA value.';

    protected $alpha;

    /**
     * @return Rgba
     */
    public function toRgba()
    {
        return $this->toRgb()->toRgba()->alpha($this->alpha());
    }

    protected function validate($code)
    {
        list($class, $index) = property_exists($this, 'lightness') ? ['hsl', 2] : ['hsv', 3];
        $color = str_replace(["{$class}a", '(', ')', ' ', '%'], '', DefinedColor::find($code, $index));
        if (substr_count($color, ',') == 2) {
            $color = "{$color},0.0";
        }
        if (preg_match('/^(\d{1,3}),(\d{1,3}),(\d{1,3}),(\d\.\d)$/', $color, $matches)) {
            if ($matches[1] > 360 || $matches[2] > 100 || $matches[3] > 100 || $matches[4] > 1) {
                return false;
            }
            return $color;
        }
        return false;
    }

    protected function initialize($color)
    {
        list($this->hue, $this->saturation, $this->lightness, $this->alpha) = explode(',', $color);
    }

    /**
     * @param int|string $alpha
     *
     * @return int|string|$this
     */
    public function alpha($alpha = null)
    {
        if ($alpha !== null) {
            $this->alpha = $alpha <= 1 ? $alpha : 0;
            return $this;
        }
        return (double) $this->alpha;
    }

    public function values()
    {
        return array_merge(parent::values(), [$this->alpha]);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'hsla(' . implode(',', $this->values()) . ')';
    }
}
