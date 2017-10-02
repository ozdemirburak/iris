<?php


namespace OzdemirBurak\Iris\Tests\Color;


use OzdemirBurak\Iris\Color\Rgba;
use OzdemirBurak\Iris\Exceptions\InvalidColorException;
use PHPUnit\Framework\TestCase;

class RgbaTest extends TestCase
{

	/** @test */
	public function can_construct_from_digit_string()
	{
		$rgba = new Rgba("rgba(255,0,0,0.3)");
		$this->assertEquals(255, $rgba->red());
		$this->assertEquals(0, $rgba->blue());
		$this->assertEquals(0, $rgba->green());
		$this->assertEquals(0.3, $rgba->alpha());
		$this->assertEquals([255, 0, 0, 0.3], $rgba->values());
	}

	/** @test */
	public function can_construct_on_predefined_string()
	{
		$rgba = new Rgba('FUSCHIA');
		$this->assertEquals(255, $rgba->red());
		$this->assertEquals(0, $rgba->green());
		$this->assertEquals(255, $rgba->blue());
		$this->assertEquals(0, $rgba->alpha());
		$this->assertEquals([255, 0, 255, 0], $rgba->values());
	}

	/** @test */
	public function cannot_construct_on_invalid_alpha()
	{
		try {
			new Rgba("rgba(255,0,0,1.2)");
		} catch (InvalidColorException $e) {
			$this->assertEquals($e->getMessage(), 'Invalid RGBA value.');
			return;
		}
		$this->fail('Exception has not been raised.');
	}

	/** @test */
	public function can_convert_to_rgba_from_rgb()
	{
		$rgba = new Rgba("rgba(255,0,0,0.3)");

		$rgb = $rgba->toRgb();
		$this->assertEquals(255, $rgb->red());
		$this->assertEquals(0, $rgb->green());
		$this->assertEquals(0, $rgb->blue());
	}
}