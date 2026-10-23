<?php

namespace OzdemirBurak\Iris\Tests;

use OzdemirBurak\Iris\Color\Factory;
use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Hexa;
use OzdemirBurak\Iris\Color\Hsl;
use OzdemirBurak\Iris\Color\Hsla;
use OzdemirBurak\Iris\Color\Oklch;
use OzdemirBurak\Iris\Color\Rgb;
use OzdemirBurak\Iris\Color\Rgba;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class FeaturesTest extends TestCase
{
    #[Group('contrast')]
    public function testLuminance()
    {
        $this->assertEqualsWithDelta(1.0, (new Hex('#fff'))->luminance(), 1e-9);
        $this->assertEqualsWithDelta(0.0, (new Hex('#000'))->luminance(), 1e-9);
        $this->assertEqualsWithDelta(0.2126, (new Hex('#f00'))->luminance(), 1e-9);
        $this->assertEqualsWithDelta(0.7152, (new Hsl('120,100%,50%'))->luminance(), 1e-9);
    }

    #[Group('contrast')]
    public function testContrastMatchesWcagReferenceValues()
    {
        // Reference values from the WebAIM contrast checker, which cuts the ratio to 2 decimals (3.9985 shows as 3.99)
        $white = new Hex('#fff');
        $this->assertEqualsWithDelta(21.0, (new Hex('#000'))->contrast($white), 0.005);
        $this->assertEqualsWithDelta(1.0, $white->contrast($white), 0.005);
        $this->assertEqualsWithDelta(4.54, (new Hex('#767676'))->contrast($white), 0.005);
        $this->assertEqualsWithDelta(4.48, (new Hex('#777777'))->contrast($white), 0.005);
        $this->assertEqualsWithDelta(3.998, (new Hex('#ff0000'))->contrast($white), 0.001);
        $this->assertEqualsWithDelta(5.25, (new Hex('#000'))->contrast(new Rgb('255,0,0')), 0.005);
        // The ratio does not depend on the order
        $this->assertEqualsWithDelta($white->contrast(new Hex('#767676')), (new Hex('#767676'))->contrast($white), 1e-9);
    }

    #[Group('contrast')]
    public function testContrastBlendsTextWithAlphaOntoTheBackground()
    {
        // rgba(0,0,0,0.5) on white looks like rgb(128,128,128)
        $this->assertEqualsWithDelta(
            (new Rgb('128,128,128'))->contrast(new Hex('#fff')),
            (new Rgba('0,0,0,0.5'))->contrast(new Hex('#fff')),
            1e-9
        );
        // On black, the same text is black
        $this->assertEqualsWithDelta(1.0, (new Rgba('0,0,0,0.5'))->contrast(new Hex('#000')), 1e-9);
    }

    #[Group('contrast')]
    public function testReadability()
    {
        $white = new Hex('#fff');
        $this->assertTrue((new Hex('#767676'))->isReadable($white));
        $this->assertFalse((new Hex('#777777'))->isReadable($white));
        $this->assertTrue((new Hex('#777777'))->isReadable($white, 3), 'Large text needs 3:1');
        // isLight() calls red dark, but black text is more readable on red than white text
        $this->assertEquals('#000000', (string) (new Hex('#ff0000'))->mostReadable());
        $this->assertEquals('#ffffff', (string) (new Hex('#0000ff'))->mostReadable());
        $candidates = [new Hex('#ff0'), new Hex('#ccc'), new Hex('#333')];
        $this->assertSame($candidates[2], (new Hex('#eee'))->mostReadable($candidates));
    }

    #[Group('distance')]
    public function testDistance()
    {
        $red = new Hex('#ff0000');
        $this->assertSame(0.0, $red->distance(new Hsl('0,100%,50%')));
        $this->assertSame(0.0, $red->distance(new Oklch('oklch(62.8% 0.2577 29.23)')));
        $this->assertEqualsWithDelta(1.0, (new Hex('#000'))->distance(new Hex('#fff')), 1e-4);
        $this->assertLessThan(0.02, $red->distance(new Hex('#fe0000')));
        $this->assertGreaterThan(0.3, $red->distance(new Hex('#0000ff')));
    }

    #[Group('distance')]
    public function testEquals()
    {
        $red = new Hex('#ff0000');
        $this->assertTrue($red->equals(new Hsla('0,100%,50%,1')));
        $this->assertTrue($red->equals(new Hexa('#ff0000ff')));
        $this->assertFalse($red->equals(new Rgba('255,0,0,0.5')), 'A different alpha');
        $this->assertFalse($red->equals(new Hex('#fe0000')));
        $this->assertTrue($red->equals(new Hex('#fe0000'), 0.02), 'Within the tolerance');
        $this->assertTrue((new Rgba('255,0,0,0.5'))->equals(new Hexa('#ff000080')), '0x80 / 255 is 0.50 to 2 decimals');
    }

    #[Group('helpers')]
    public function testToName()
    {
        $this->assertSame('tomato', (new Hex('#ff6347'))->toName());
        $this->assertSame('tomato', (new Hex('#ff6347'))->toHsl()->toName());
        $this->assertSame('aqua', (new Rgb('0,255,255'))->toName(), 'The first name in alphabetical order');
        $this->assertSame('transparent', (new Rgba('0,0,0,0'))->toName());
        $this->assertNull((new Hex('#123456'))->toName());
        $this->assertNull((new Rgba('255,99,71,0.5'))->toName(), 'A translucent color has no name');
    }

    #[Group('helpers')]
    public function testInvertAndComplement()
    {
        $this->assertEquals('#00ffff', (string) (new Hex('#ff0000'))->invert());
        $this->assertEquals('rgba(245,235,225,0.5)', (string) (new Rgba('10,20,30,0.5'))->invert());
        $this->assertEquals('#00ffff', (string) (new Hex('#ff0000'))->complement());
        $this->assertEquals('hsl(250,50%,40%)', (string) (new Hsl('70,50%,40%'))->complement());
    }

    #[Group('helpers')]
    public function testJsonEncodeGivesTheStringForm()
    {
        $this->assertSame('["#ff0000","rgba(1,2,3,0.5)"]', json_encode([new Hex('#f00'), new Rgba('1,2,3,0.5')]));
    }

    #[Group('factory-init')]
    public function testSafeParsing()
    {
        $this->assertInstanceOf(Hex::class, Factory::tryInit('#fff'));
        $this->assertNull(Factory::tryInit('not a color'));
        $this->assertNull(Factory::tryInit('120,50%,50%'), 'An ambiguous string');
        $this->assertTrue(Factory::isValid('rgb(1 2 3 / 50%)'));
        $this->assertFalse(Factory::isValid(''));
    }
}
