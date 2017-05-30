<?php

namespace OzdemirBurak\Iris\Tests\Helpers;

use OzdemirBurak\Iris\Helpers\DefinedColor;
use PHPUnit\Framework\TestCase;

class DefinedColorTest extends TestCase
{
    /**
     * @group defined-color
     */
    public function testAll()
    {
        $this->assertNotEmpty(DefinedColor::get());
    }

    /**
     * @group defined-color
     */
    public function testFind()
    {
        $this->assertEquals(DefinedColor::find('AQUA'), '00ffff');
        $this->assertEquals(DefinedColor::find('black', 1), '0,0,0');
    }
}
