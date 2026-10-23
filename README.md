# Iris - PHP Color Library

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]
[![Tests][ico-tests]][link-tests]

PHP library for color manipulation and conversion. Requires PHP 8.4+ and supports 
Hex, Hexa, RGB, RGBA, HSL, HSLA, HSV, CMYK, and OKLCH color formats.

## Install

``` bash
$ composer require ozdemirburak/iris
```

## Upgrading from 4.x

Version 5 requires PHP 8.4. Most code works without changes. Check these points, the full list is in the [change log](CHANGELOG.md):

- The manipulation methods and the conversions return a new color and do not change the color you call them on. If your code used that side effect, use the return value: `$hsl = $hsl->lighten(10);`
- The setters of `Rgb`, `Rgba`, `Hsl`, `Hsla`, `Hsv`, `Cmyk` and `Oklch` clamp a value that is out of range (`red(300)` sets 255), and the hue wraps around 360 (`hue(400)` sets 40). Before, these values were ignored. A value that is not a number is ignored, and the setter returns the color.
- Operations on `Rgba`, `Hsla`, `Hexa` and `Oklch` keep the alpha. `mix()`, `tint()`, `shade()` and `gradient()` mix the alpha of both colors and weight the colors by their alpha, like less.js and Sass.
- Blending a color with alpha onto its background rounds each channel and no longer rounds down, so some results are 1 higher: `rgba(0,0,0,0.3)` gives `rgb(179,179,179)`.
- `Oklch` keeps the alpha of `oklch(… / 0.5)`. A conversion to a model without alpha (`toHex()`, `toRgb()`, …) blends it onto white, as for `Rgba` and `Hsla`.
- `fade()`, `fadeIn()` and `fadeOut()` return `Hexa` for `Hex` and `Hexa`, and `Oklch` for `Oklch`.
- Conversions to HSV and CMYK keep one decimal, e.g. `hsv(303.5,96.7%,96.5%)`, and `Hsv::value()` returns a float.
- `Oklch::values()` includes the alpha, and a lightness without `%` that is not greater than 1 is a number from 0 to 1: `oklch(0.7 0.1 120)` is 70%.
- `Factory::init()` throws `InvalidColorException` for an empty string and for color functions it does not support, and reads 4 hex digits only with a `#`.
- If you extend the color classes: `Rgb::red()` returns `int`, `Hex::red()` returns `string`, the `$castsInteger` property is removed, and `BaseColor` has new protected methods (see the change log).

## Usage

### Hex

``` php
use OzdemirBurak\Iris\Color\Hex;

$hex = new Hex('#ff00ff'); // same as new Hex('fuchsia');
echo $hex->red(); // ff
echo $hex->green(); // 00
echo $hex->blue(); // ff
$values = $hex->values(); // ['ff', '00', 'ff']
$hexa = $hex->toHexa(); // \OzdemirBurak\Iris\Color\Hexa('ff00ffff')
$hsl = $hex->toHsl(); // \OzdemirBurak\Iris\Color\Hsl('300,100,50')
$hsla = $hex->toHsla(); // \OzdemirBurak\Iris\Color\Hsla('300,100,50,1.0')
$hsv = $hex->toHsv(); // \OzdemirBurak\Iris\Color\Hsv('300,100,100')
$rgb = $hex->toRgb(); // \OzdemirBurak\Iris\Color\Rgb('255,0,255')
$rgba = $hex->toRgba(); // \OzdemirBurak\Iris\Color\Rgba('255,0,255,1.0')
$oklch = $hex->toOklch(); // \OzdemirBurak\Iris\Color\Oklch('70.17,0.3225,328.36')
echo $hex; // #ff00ff
```

### Hexa (hex with alpha value)

``` php
use OzdemirBurak\Iris\Color\Hexa;

$hexa = new Hexa('#ff00ff4c');
echo $hexa->red(); // ff
echo $hexa->green(); // 00
echo $hexa->blue(); // ff
echo $hexa->alpha(); // 0.3 - as a float for compatibility with the other conversions
$values = $hexa->values(); // ['ff', '00', 'ff', 0.3]
$hsl = $hexa->toHsl(); // \OzdemirBurak\Iris\Color\Hsl('300,100,50')
$hsla = $hexa->toHsla(); // \OzdemirBurak\Iris\Color\Hsla('300,100,50,0.3')
$hsv = $hexa->toHsv(); // \OzdemirBurak\Iris\Color\Hsv('300,100,100')
$rgb = $hexa->toRgb(); // \OzdemirBurak\Iris\Color\Rgb('255,0,255')
$rgba = $hexa->toRgba(); // \OzdemirBurak\Iris\Color\Rgba('255,0,255,0.3')
echo $hexa; // #ff00ff4c
```

### HSL

``` php
use OzdemirBurak\Iris\Color\Hsl;

$hsl = new Hsl('hsl(300,100%,50%)'); // same as new Hsl('fuchsia');
echo $hsl->hue(); // 300
echo $hsl->saturation(); // 100
echo $hsl->lightness(); // 50
$values = $hsl->values(); // [300, '100%', '50%']
$normalizedValues = $hsl->valuesInUnitInterval(); // [300/360, 100/100, 50/100]
$hex = $hsl->toHex(); // \OzdemirBurak\Iris\Color\Hex('ff00ff')
$hexa = $hsl->toHexa(); // \OzdemirBurak\Iris\Color\Hexa('ff00ffff')
$hsv = $hsl->toHsv(); // \OzdemirBurak\Iris\Color\Hsv('300,100,100')
$rgb = $hsl->toRgb(); // \OzdemirBurak\Iris\Color\Rgb('255,0,255')
$rgba = $hsl->toRgba(); // \OzdemirBurak\Iris\Color\Rgba('255,0,255,1.0')
echo $hsl; // hsl(300,100%,50%)
```

### HSLA

``` php
use OzdemirBurak\Iris\Color\Hsla;

$hsla = new Hsla('hsla(150,100%,50%,0.3)');
echo $hsla->hue(); // 150
echo $hsla->saturation(); // 100
echo $hsla->lightness(); // 50
echo $hsla->alpha(); // 0.3
$values = $hsla->values(); // [150, '100%', '50%', 0.3]
$hex = $hsla->toHex(); // \OzdemirBurak\Iris\Color\Hex('b2ffd8')
$rgba = $hsla->toRgba(); // \OzdemirBurak\Iris\Color\Rgba('0,255,128,0.3')
$hexa = $hsla->toHexa(); // \OzdemirBurak\Iris\Color\Hexa('00ff804d')
echo $hsla; // hsla(150,100%,50%,0.3)
```

### HSV

``` php
use OzdemirBurak\Iris\Color\Hsv;

$hsv = new Hsv('hsv(300,100%,100%)'); // same as new Hsv('fuchsia');
echo $hsv->hue(); // 300
echo $hsv->saturation(); // 100
echo $hsv->value(); // 100
$values = $hsv->values(); // [300, '100%', '100%']
$normalizedValues = $hsv->valuesInUnitInterval(); // [300/360, 100/100, 100/100]
$hex = $hsv->toHex(); // \OzdemirBurak\Iris\Color\Hex('ff00ff')
$hexa = $hsv->toHexa(); // \OzdemirBurak\Iris\Color\Hexa('ff00ffff')
$hsl = $hsv->toHsl(); // \OzdemirBurak\Iris\Color\Hsl('300,100,50')
$hsla = $hsv->toHsla(); // \OzdemirBurak\Iris\Color\Hsla('300,100,50,1.0')
$hsv = $hsv->toHsv(); // \OzdemirBurak\Iris\Color\Hsv('300,100,100')
$rgb = $hsv->toRgb(); // \OzdemirBurak\Iris\Color\Rgb('255,0,255')
$rgba = $hsv->toRgba(); // \OzdemirBurak\Iris\Color\Rgba('255,0,255,1.0')
echo $hsv; // hsv(300,100%,100%)
```

### RGB

``` php
use OzdemirBurak\Iris\Color\Rgb;

$rgb = new Rgb('rgb(255, 0, 255)'); // same as new Rgb('fuchsia');

echo $rgb->red(); // 255
echo $rgb->green(); // 0
echo $rgb->blue(); // 255
$values = $rgb->values(); // [255, 0, 255]
$hex = $rgb->toHex(); // \OzdemirBurak\Iris\Color\Hex('ff00ff')
$hexa = $rgb->toHexa(); // \OzdemirBurak\Iris\Color\Hexa('ff00ffff')
$hsl = $rgb->toHsl(); // \OzdemirBurak\Iris\Color\Hsl('300,100,50')
$hsla = $rgb->toHsla(); // \OzdemirBurak\Iris\Color\Hsla('300,100,50,1.0')
$hsv = $rgb->toHsv(); // \OzdemirBurak\Iris\Color\Hsv('300,100,100')
$rgb = $rgb->toRgb(); // \OzdemirBurak\Iris\Color\Rgb('255,0,255')
$rgba = $rgb->toRgba(); // \OzdemirBurak\Iris\Color\Rgba('255,0,255,1.0')
echo $rgb; // rgb(255,0,255)
```

### RGBA

``` php
use OzdemirBurak\Iris\Color\Rgba;

$rgba = new Rgba('rgba(93,111,222,0.33)');

echo $rgba->red(); // 93
echo $rgba->green(); // 111
echo $rgba->blue(); // 222
echo $rgba->alpha(); // 0.33
$hex = $rgba->background((new Hex('ccc'))->toRgb())->toHex(); // \OzdemirBurak\Iris\Color\Hex('a7add2')
$hexa = $rgba->toHexa(); // \OzdemirBurak\Iris\Color\Hexa('5d6fde54') - preserves original RGB
echo $rgba; // rgba(93,111,222,0.33)
```

### CMYK
```php
use OzdemirBurak\Iris\Color\Cmyk;

$cmyk = new Cmyk('cmyk(0,100,0,0)');

echo $cmyk->cyan(); // 0
echo $cmyk->magenta(); // 100
echo $cmyk->yellow(); // 0
echo $cmyk->black(); // 0

$values = $cmyk->values(); // [0, 100, 0, 0]
$hex = $cmyk->toHex(); // OzdemirBurak\Iris\Color\Hex('ff00ff')
$hexa = $cmyk->toHexa(); // OzdemirBurak\Iris\Color\Hexa('ff00ffff')
$hsl = $cmyk->toHsl(); // OzdemirBurak\Iris\Color\Hsl('300,100,50')
$hsla = $cmyk->toHsla(); // OzdemirBurak\Iris\Color\Hsla('300,100,50,1.0')
$hsv = $cmyk->toHsv(); // OzdemirBurak\Iris\Color\Hsv('300,100,100')
$rgb = $cmyk->toRgb(); // OzdemirBurak\Iris\Color\Rgb('255,0,255')
$rgba = $cmyk->toRgba(); // OzdemirBurak\Iris\Color\Rgba('255,0,255,1.0')

echo $cmyk; // cmyk(0,100,0,0)
```

### OKLCH

```php
use OzdemirBurak\Iris\Color\Oklch;
use OzdemirBurak\Iris\Color\Hex;

// Comma-separated syntax
$oklch = new Oklch('oklch(70%, 0.15, 150)');

// Space-separated syntax (CSS Color Level 4)
$oklch = new Oklch('oklch(40.1% 0.123 21.57)');
$oklch = new Oklch('oklch(0.401 0.123 21.57)'); // lightness without % is a number from 0 to 1 (above 1 it is a percentage, as in 4.x)
$oklch = new Oklch('oklch(59.69% 0.156 49.77)');

echo $oklch->lightness(); // 59.69
echo $oklch->chroma(); // 0.156
echo $oklch->hue(); // 49.77

$values = $oklch->values(); // [59.69, 0.156, 49.77, 1.0], the last value is the alpha
$hex = $oklch->toHex(); // OzdemirBurak\Iris\Color\Hex
$rgb = $oklch->toRgb(); // OzdemirBurak\Iris\Color\Rgb
$hsl = $oklch->toHsl(); // OzdemirBurak\Iris\Color\Hsl

echo $oklch; // oklch(59.69% 0.156 49.77)

// Convert from other formats to OKLCH
$hex = new Hex('#ff0000');
$oklch = $hex->toOklch();
echo $oklch; // oklch(62.8% 0.2577 29.23)

// Alpha
$oklch = new Oklch('oklch(62.8% 0.2577 29.23 / 0.5)');
echo $oklch->alpha(); // 0.5
echo $oklch->toRgba(); // rgba(255,0,0,0.5)
echo $oklch->toHex(); // #ff8080, blended onto white like Rgba and Hsla
echo $oklch; // oklch(62.8% 0.2577 29.23 / 0.5), the alpha is only written when it is below 1
```

**Note:** Relative color syntax with the `from` keyword (e.g., `oklch(from green l c h)`) is not supported.

### Via Factory

If you do not know what the color string will be (for example, you're getting it from a group of rows from a database),
then you can try using Factory to instantiate an appropriate color class:

``` php
use OzdemirBurak\Iris\Color\Factory;

$color = Factory::init('rgba(93,111,222,0.33)');
echo $color->red(); // 93
echo $color->green(); // 111
echo $color->blue(); // 222
echo $color->alpha(); // 0.33

// No exception for a string that is not a color, e.g. for validation rules
$color = Factory::tryInit('not a color'); // null
var_dump(Factory::isValid('rgb(10 20 30 / 50%)')); // bool(true)
```

### Supported syntax

All color classes and `Factory::init()` accept the legacy comma syntax and the CSS Color Level 4 syntax. Values out of range are rejected, except the hue, which wraps around 360:

``` php
Factory::init('rgb(10 20 30)');             // Rgb, same as rgb(10,20,30)
Factory::init('rgb(10 20 30 / 50%)');       // Rgba, same as rgba(10,20,30,0.5)
Factory::init('rgb(100% 0% 50%)');          // Rgb('255,0,128')
Factory::init('hsl(0.5turn 50% 50% / .3)'); // Hsla('180,50,50,0.3'), also deg, grad and rad
Factory::init('#f0f8');                     // Hexa('ff00ff88')
Factory::init('transparent');               // Rgba('0,0,0,0'), also for new Hexa() and new Hsla()
```

The setters of `Rgb`, `Rgba`, `Hsl`, `Hsla`, `Hsv`, `Cmyk` and `Oklch` clamp values that are out of range, the hue wraps around 360, and values that are not numbers are ignored. The setters of `Hex` and `Hexa` take hex digits:

``` php
echo (new Rgb('10,20,30'))->red(300); // rgb(255,20,30)
echo (new Hsl('10,50,50'))->hue(400); // hsl(40,50%,50%)
echo (new Hex('#0a141e'))->red('ff'); // #ff141e
```

### Color Cloning

You can clone a color object to make a copy and modify it as needed.

```php
use OzdemirBurak\Iris\Color\Hex;

$original = new Hex('#b2b2b2');
$cloned = $original->clone()->toHexa()->alpha(0.5); // OzdemirBurak\Iris\Color\Hexa('#b2b2b280')
```

### Color Manipulation

The manipulation methods return a new color and do not change the color you call them on.
Colors with an alpha channel (Hexa, Rgba, Hsla, Oklch) keep their alpha; `mix()`, `tint()`, `shade()` and `gradient()` mix the alpha of both colors.

#### Saturation

Saturate or desaturate by a percent.

``` php
echo (new Hsl('90,80%,50%'))->saturate(20)->toHex(); // #80ff00
echo (new Hsl('90, 80%, 50%'))->desaturate(20)->toRgb(); // rgb(128,204,51)
echo (new Hex('#80cc33'))->grayscale(); // #808080, same as desaturate 100
```

#### Lightness

Lighten, darken or brighten by a percent.

``` php
$hex = new Hex('#333');
echo $hex->lighten(20); // #666666
echo $hex->darken(20); // #000000
echo $hex->brighten(20); // #666666
```

#### Spin

Spin the hue by an angle in degrees. The result wraps around 360.

``` php
$hex = (new Hsl('10,90%,50'))->spin(30)->toHex();
echo $hex; // #f2a60d
```

#### Mix

Mix by a percent.

``` php
$hex = new Hex('#000');
echo $hex->mix(new Hex('#fff'), 50); // #808080
```

#### Tint

Mix color with white by a percent.

``` php
$hex = new Hex('#000');
echo $hex->tint(50); // #808080
```

#### Shade

Mix color with black by a percent.

``` php
$hex = new Hex('#FFF');
echo $hex->shade(50); // #808080
```

#### Fade

Set the absolute opacity of a color by a percent.

``` php
$hsl = new Hsl('90,90,50');
echo $hsl->fade(10); // hsla(90,90%,50%,0.1)

$rgb = new Rgb('128,242,13');
echo $rgb->fade(10); // rgba(128,242,13,0.1)
```

#### FadeIn

Increase the opacity of a color by a percent.

``` php
$hsla = new Hsla('90,90,50,0.3');
echo $hsla->fadeIn(10); // hsla(90,90%,50%,0.4)

$rgba = new Rgba('128,242,13,0.3');
echo $rgba->fadeIn(10); // rgba(128,242,13,0.4)
```

#### FadeOut

Decrease the opacity of a color by a percent.

``` php
$hsla = new Hsla('90,90,50,0.3');
echo $hsla->fadeOut(10); // hsla(90,90%,50%,0.2)

$rgba = new Rgba('128,242,13,0.3');
echo $rgba->fadeOut(10); // rgba(128,242,13,0.2)
```

#### Is light or dark

Determine if color is dark or light color.

``` php
$hex = new Hex('#000');
var_dump($hex->isLight()); // bool(false)
var_dump($hex->isDark()); // bool(true)
```

`isLight()` uses the luma of the color. To choose a text color, use the WCAG contrast below.

#### Contrast and readability

WCAG 2 relative luminance and contrast ratio. A text color with alpha is blended onto the background first.

``` php
$white = new Hex('#fff');
echo (new Hex('#000'))->luminance(); // 0
echo (new Hex('#767676'))->contrast($white); // 4.54..., from 1 to 21
var_dump((new Hex('#777777'))->isReadable($white)); // bool(false), normal text needs 4.5 (WCAG AA)
var_dump((new Hex('#777777'))->isReadable($white, 3)); // bool(true), large text needs 3
echo (new Hex('#ff0000'))->mostReadable(); // #000000, the best text color on red: black or white
echo (new Hex('#eee'))->mostReadable([new Hex('#ff0'), new Hex('#333')]); // #333333
```

#### Distance and equality

The color difference deltaE OK (CSS Color 4), from 0 for the same color. A distance below about 0.02 is hard to see.

``` php
$red = new Hex('#ff0000');
echo $red->distance(new Hsl('0,100%,50%')); // 0
var_dump($red->equals(new Hsl('0,100%,50%'))); // bool(true), in any model, and the alpha must be the same
var_dump($red->equals(new Hex('#fe0000'))); // bool(false)
var_dump($red->equals(new Hex('#fe0000'), 0.02)); // bool(true)
```

#### Names, invert and complement

``` php
echo (new Hex('#ff6347'))->toName(); // tomato, null when the color has no name
echo (new Hex('#ff0000'))->invert(); // #00ffff, the alpha is kept
echo (new Hex('#ff0000'))->complement(); // #00ffff, the same as spin(180)
echo json_encode([new Hex('#f00'), new Rgba('1,2,3,0.5')]); // ["#ff0000","rgba(1,2,3,0.5)"]
```

#### Gradient

Generate an array of colors (gradient) between two or more colors.

``` php
use OzdemirBurak\Iris\Color\Hex;
use OzdemirBurak\Iris\Color\Rgb;

// Two-color gradient: 5 steps from red to blue
$gradient = (new Hex('#ff0000'))->gradient(new Hex('#0000ff'), 5);
// ['#ff0000', '#bf0040', '#800080', '#4000bf', '#0000ff']

// Multi-color gradient with pivot: red → yellow → green in 7 steps
$gradient = (new Hex('#ff0000'))->gradient([new Hex('#ffff00'), new Hex('#00ff00')], 7);
// ['#ff0000', '#ff5500', '#ffaa00', '#ffff00', '#aaff00', '#55ff00', '#00ff00']

// Works with any color type - output matches the starting color type
$gradient = (new Rgb('255,0,0'))->gradient(new Rgb('0,0,255'), 3);
// [rgb(255,0,0), rgb(128,0,128), rgb(0,0,255)]
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test     # PHPUnit
$ composer analyse  # PHPStan, at the max level
$ composer check    # both
```

## Contributing

Pull requests are welcome. Run `composer check` (PHPStan and the tests) before you open one, and add tests for your change.

## Credits

- [Burak Özdemir][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ozdemirburak/iris.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ozdemirburak/iris.svg?style=flat-square
[ico-tests]: https://img.shields.io/github/actions/workflow/status/ozdemirburak/iris/run-tests.yml?branch=master&label=tests&style=flat-square

[link-packagist]: https://packagist.org/packages/ozdemirburak/iris
[link-downloads]: https://packagist.org/packages/ozdemirburak/iris
[link-tests]: https://github.com/ozdemirburak/iris/actions/workflows/run-tests.yml
[link-author]: https://github.com/ozdemirburak
[link-contributors]: ../../contributors
