<?php

namespace OzdemirBurak\Iris\Tests;

use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Color\Rgba;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class OperationsTest extends TestCase
{
    #[Group('operations-saturate')]
    public function testSaturation()
    {
        $this->assertEquals(new Hex('#80ff00'), (new Hsl('90, 80%, 50%'))->saturate(20)->toHex());
        $this->assertEquals(new Hex('#80cc33'), (new Hsl('90, 80%, 50%'))->desaturate(20)->toHex());
        $this->assertEquals(new Hex('#80b34d'), (new Hex('#80cc33'))->desaturate(20));
        $this->assertEquals(new Hex('#808080'), (new Hex('#80cc33'))->grayscale());
    }

    #[Group('operations-lighten')]
    public function testLightness()
    {
        $this->assertEquals(new Hex('#b3f075'), (new Hsl('90, 80%, 50%'))->lighten(20)->toHex());
        $this->assertEquals(new Hex('#4d8a0f'), (new Hsl('90, 80%, 50%'))->darken(20)->toHex());
        $this->assertEquals(new Hex('#4d7a1f'), (new Hex('#80cc33'))->darken(20));
        $this->assertEquals(new Hex('#b3ff66'), (new Hex('#80cc33'))->brighten(20));
    }

    #[Group('operations-spin')]
    public function testSpin()
    {
        $this->assertEquals(new Hex('#f2a60d'), (new Hsl('10,90%,50'))->spin(30)->toHex());
        $this->assertEquals(new Hex('#f20d59'), (new Hsl('10,90%,50'))->spin(-30)->toHex());
    }

    #[Group('operations-mix')]
    public function testMix()
    {
        $this->assertEquals(new Hex('#808080'), (new Hex('#000'))->mix(new Hex('#fff')));
        $this->assertEquals(new Hex('#ff8000'), (new Hex('#ff0000'))->mix(new Hex('#ffff00')));
    }

    #[Group('operations-is-light')]
    public function testIsLight()
    {
        $this->assertFalse((new Hex('#000000'))->isLight());
        $this->assertTrue((new Hex('#ffffff'))->isLight());
        $this->assertTrue((new Hex('#808080'))->isLight());
        $this->assertTrue((new Hex('#888888'))->isLight());
        $this->assertFalse((new Hex('#777777'))->isLight());
        $this->assertFalse((new Hex('#ff0000'))->isLight());
        $this->assertTrue((new Hex('#ffff00'))->isLight());
    }

    #[Group('operations-is-dark')]
    public function testIsDark()
    {
        $this->assertTrue((new Hex('#000000'))->isDark());
        $this->assertFalse((new Hex('#ffffff'))->isDark());
    }

    #[Group('operations-tint')]
    public function testTint()
    {
        $this->assertEquals(new Hex('#80bfff'), (new Hex('#007fff'))->tint(50));
        $this->assertEquals(new Hex('#80bfff'), (new Hex('#007fff'))->tint());
    }

    #[Group('operations-shade')]
    public function testShade()
    {
        $this->assertEquals(new Hex('#004080'), (new Hex('#007fff'))->shade(50));
        $this->assertEquals(new Hex('#004080'), (new Hex('#007fff'))->shade());
    }

    #[Group('operations-fade')]
    public function testFade()
    {
        $this->assertEquals(new Hsla('90,90,50,0.1'), (new Hsl('90,90,50'))->fade(10));
        $this->assertEquals(new Rgba('128,242,13,0.1'), (new Rgb('128,242,13'))->fade(10));
    }

    #[Group('operations-fadeIn')]
    public function testFadeIn()
    {
        $this->assertEquals(new Hsla('90,90,50,0.4'), (new Hsla('90,90,50,0.3'))->fadeIn(10));
        $this->assertEquals(new Rgba('128,242,13,0.4'), (new Rgba('128,242,13,0.3'))->fadeIn(10));
    }

    #[Group('operations-fadeOut')]
    public function testFadeOut()
    {
        $this->assertEquals('hsla(90,90%,50%,0.2)', (string) (new Hsla('90,90,50,0.3'))->fadeOut(10));
        $this->assertEquals('rgba(128,242,13,0.2)', (string) (new Rgba('128,242,13,0.3'))->fadeOut(10));
    }
}
