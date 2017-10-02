<?php


namespace OzdemirBurak\Iris\Color;


use OzdemirBurak\Iris\Helpers\DefinedColor;

class Rgba extends Rgb
{
	protected $alpha;

	protected $exceptionMessage = 'Invalid RGBA value.';

	protected function validate ($code)
	{
		$color = str_replace(['rgba', '(', ')', ' '], '', DefinedColor::find($code, 1));
		if(substr_count($color, ',') == 2) {
			$color = "{$color},0.0";
		}
		if (preg_match('/^(\d{1,3}),(\d{1,3}),(\d{1,3}),(\d\.\d)$/', $color, $matches)) {
			if ($matches[1] > 255 || $matches[2] > 255 || $matches[3] > 255 || $matches[4] > 1) {
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
	protected function initialize($color)
	{
		$colors = explode(',', $color);
		list($this->red, $this->green, $this->blue) = array_map('intval', $colors);
		$this->alpha = doubleval($colors[3]);
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
		return $this->alpha;
	}

	public function values ()
	{
		return array_merge(parent::values(), [$this->alpha]);
	}

	public function toRgb ()
	{
		return new Rgb("{$this->red()},{$this->green()},{$this->blue()}");
	}

	public function __toString ()
	{
		return 'rgba(' . implode(',', $this->values()) . ')';
	}


}