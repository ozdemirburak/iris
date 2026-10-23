<?php

namespace OzdemirBurak\Iris\Tests\Helpers;

use OzdemirBurak\Iris\Helpers\DefinedColor;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class DefinedColorTest extends TestCase
{
    #[Group('defined-color')]
    public function testAll()
    {
        $colors = DefinedColor::get();
        $this->assertCount(148, $colors, 'The 148 CSS named colors');
        foreach ($colors as $name => [$hex, $rgb]) {
            $this->assertMatchesRegularExpression('/^[a-z]+$/', $name);
            $this->assertMatchesRegularExpression('/^[0-9a-f]{6}$/', $hex, $name);
            $this->assertSame(implode(',', array_map('hexdec', str_split($hex, 2))), $rgb, "$name: the rgb column");
        }
    }

    #[Group('defined-color')]
    public function testFind()
    {
        $this->assertSame('00ffff', DefinedColor::find('AQUA'));
        $this->assertSame('0,0,0', DefinedColor::find('black', 1));
        $this->assertSame('#abc', DefinedColor::find(' #ABC '), 'Not a name: trimmed and lowercase');
    }

    #[Group('defined-color')]
    public function testHexAndName()
    {
        $this->assertSame('ff6347', DefinedColor::hex(" Tomato\f"));
        $this->assertNull(DefinedColor::hex('not a color'));
        $this->assertSame('tomato', DefinedColor::name('#FF6347'));
        $this->assertSame('aqua', DefinedColor::name('00ffff'), 'The first name in alphabetical order');
        $this->assertNull(DefinedColor::name('123456'));
    }
}
