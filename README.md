# Iris - PHP Color Library

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

PHP library for color manipulation and conversion. 

## Install

Via Composer

``` bash
$ composer require ozdemirburak/iris
```

## Usage

### Hex

``` php
use OzdemirBurak\Iris\Color\Hex;

$hex = new Hex('#ff00ff'); // same as new Hex('fuchsia');
echo $hex->red(); // ff
echo $hex->green(); // 00
echo $hex->blue(); // ff
echo $hex->values(); // ['ff', '00', 'ff']
$hexa = $hex->toHexa(); // \OzdemirBurak\Iris\Color\Hexa('ff00ffff')
$hsl = $hex->toHsl(); // \OzdemirBurak\Iris\Color\Hsl('300,100,50')
$hsla = $hex->toHsla(); // \OzdemirBurak\Iris\Color\Hsla('300,100,50,1.0')
$hsv = $hex->toHsv(); // \OzdemirBurak\Iris\Color\Hsv('300,100,100')
$rgb = $hex->toRgb(); // \OzdemirBurak\Iris\Color\Rgb('255,0,255')
$rgba = $hex->toRgba(); // \OzdemirBurak\Iris\Color\Rgba('255,0,255,1.0')
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
echo $hexa->values(); // ['ff', '00', 'ff', 0.3]
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
$hex = $hsla->toRgba(); // \OzdemirBurak\Iris\Color\Rgba('0,255,128,0.3')
$hexa = $hsla->toHexa(); // \OzdemirBurak\Iris\Color\Hexa('ff00ff4c')
echo $hsla; // hsla(150,100%,50%,0.3)
```

### HSV

``` php
use OzdemirBurak\Iris\Color\Hsv;

$hsv = new Hsv('hsv(300,100%,100%)'); // same as new Hsv('fuchsia');
echo $hsv->hue(); // 300 
echo $hsv->saturation(); // 100
echo $hsv->value(); // 100
$values = $hsv->values(); // [100, '100%', '100%']
$normalizedValues = $hsv->valuesInUnitInterval(); // [300/360, 100/100, 100/100]
$hex = $hsv->toHex(); // \OzdemirBurak\Iris\Color\Hex('ff00ff')
$hexa = $hsv->toHexa(); // \OzdemirBurak\Iris\Color\Hexa('ff00ffff')
$hsl = $hsv->toHsl(); // \OzdemirBurak\Iris\Color\Hsl('300,100,50')
$hsla = $hsv->toHsla(); // \OzdemirBurak\Iris\Color\Hsla('300,100,50,1.0')
$hsv = $hsv->toHsv(); // \OzdemirBurak\Iris\Color\Hsv('300,100,100')
$rgb = $hsv->toRgb(); // \OzdemirBurak\Iris\Color\Rgb('255,0,255')
$rgba = $hsv->toRgba(); // \OzdemirBurak\Iris\Color\Rgba('255,0,255,1.0')
echo $hsv; // hsl(300,100%,100%)
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
echo $rgba->alpha(); // 0.33,
$hex = $rgba->background((new Hex('ccc'))->toRgb())->toHex(); // \OzdemirBurak\Iris\Color\Hex('a7add1')
$hexa = $rgba->toHexa(); // \OzdemirBurak\Iris\Color\Hexa('a7add154')
echo $rgba; // rgba(127,127,127,0.5)
```

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
```


### Color Manipulation

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

Spin by an angle [-360, 360]

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

#### Mix in HSV space

Same as mix(), but interpolation is done in HSV space rather than in
RGB space. The result may be closer to what the human eye perceives as
"the color in between".

``` php
$hex = new Hex('#ff000');
echo $hex->mixInHsv(new Hex('#00ff00'), 50); // ##ffff00
                                             // mix() would return #808000
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
echo $hex->isLight(); // false
echo $hex->isDark(); // true
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Burak Özdemir][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ozdemirburak/iris.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ozdemirburak/iris/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ozdemirburak/iris.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/ozdemirburak/iris
[link-travis]: https://travis-ci.org/ozdemirburak/iris
[link-downloads]: https://packagist.org/packages/ozdemirburak/iris
[link-author]: https://github.com/ozdemirburak
[link-contributors]: ../../contributors
