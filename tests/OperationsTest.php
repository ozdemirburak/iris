<?php

namespace OzdemirBurak\Iris\Tests;

use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hsl;
use PHPUnit\Framework\TestCase;

class OperationsTest extends TestCase
{
    /**
     * @group operations-saturate
     */
    public function testSaturation()
    {
        $this->assertEquals(new Hex('#80ff00'), (new Hsl('90, 80%, 50%'))->saturate(20)->toHex());
        $this->assertEquals(new Hex('#80cc33'), (new Hsl('90, 80%, 50%'))->desaturate(20)->toHex());
        $this->assertEquals(new Hex('#80b34d'), (new Hex('#80cc33'))->desaturate(20));
        $this->assertEquals(new Hex('#808080'), (new Hex('#80cc33'))->grayscale());
    }

    /**
     * @group operations-lighten
     */
    public function testLightness()
    {
        $this->assertEquals(new Hex('#b3f075'), (new Hsl('90, 80%, 50%'))->lighten(20)->toHex());
        $this->assertEquals(new Hex('#4d8a0f'), (new Hsl('90, 80%, 50%'))->darken(20)->toHex());
        $this->assertEquals(new Hex('#4d7a1f'), (new Hex('#80cc33'))->darken(20));
        $this->assertEquals(new Hex('#b3ff66'), (new Hex('#80cc33'))->brighten(20));
    }

    /**
     * @group operations-spin
     */
    public function testSpin()
    {
        $this->assertEquals(new Hex('#f2a60d'), (new Hsl('10,90%,50'))->spin(30)->toHex());
        $this->assertEquals(new Hex('#f20d59'), (new Hsl('10,90%,50'))->spin(-30)->toHex());
    }

    /**
     * @group operations-mix
     */
    public function testMix()
    {
        $this->assertEquals(new Hex('#808080'), (new Hex('#000'))->mix(new Hex('#fff')));
        $this->assertEquals(new Hex('#ff8000'), (new Hex('#ff0000'))->mix(new Hex('#ffff00')));
    }

    /**
     * @group operations-is-light
     */
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

    /**
     * @group operations-is-dark
     */
    public function testIsDark()
    {
        $this->assertTrue((new Hex('#000000'))->isDark());
        $this->assertFalse((new Hex('#ffffff'))->isDark());
    }
}
