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

$hex = new Hex('#ff00ff'); // same as new Hex('fuschia');
$hex->red(); // ff
$hex->green(); // 00
$hex->blue(); // ff
$hex->values(); // ['ff', '00', 'ff']
$hex->toHex(); // \OzdemirBurak\Iris\Color\Hex('ff00ff')
$hex->toHsl(); // \OzdemirBurak\Iris\Color\Hsl('300,100,50')
$hex->toHsv(); // \OzdemirBurak\Iris\Color\Hsv('300,100,100')
$hex->toRgb(); // \OzdemirBurak\Iris\Color\Rgb('255,0,255')
echo $hex; // #ff00ff
```

### HSL

``` php
use OzdemirBurak\Iris\Color\Hex;

$hsl = new Hsl('hsl(300,100%,50%)'); // same as new Hsl('fuschia');
$hsl->hue(); // 300 
$hsl->saturation(); // 100
$hsl->lightness(); // 50
$hsl->values(); // [300, '100%', '50%']
$hsl->valuesInUnitInterval(); // [300/360, 100/100, 50/100]
$hsl->toHex(); // \OzdemirBurak\Iris\Color\Hex('ff00ff')
$hsl->toHsl(); // \OzdemirBurak\Iris\Color\Hsl('300,100,50')
$hsl->toHsv(); // \OzdemirBurak\Iris\Color\Hsv('300,100,100')
$hsl->toRgb(); // \OzdemirBurak\Iris\Color\Rgb('255,0,255')
echo $hsl; // hsl(300,100%,50%)
```

### HSV

``` php
use OzdemirBurak\Iris\Color\Hsv;

$hsv = new Hsv('hsv(300,100%,100%)'); // same as new Hsv('fuschia');
$hsv->hue(); // 300 
$hsv->saturation(); // 100
$hsv->value(); // 100
$hsv->values(); // [100, '100%', '100%']
$hsv->valuesInUnitInterval(); // [300/360, 100/100, 100/100]
$hsv->toHex(); // \OzdemirBurak\Iris\Color\Hex('ff00ff')
$hsv->toHsl(); // \OzdemirBurak\Iris\Color\Hsl('300,100,50')
$hsv->toHsv(); // \OzdemirBurak\Iris\Color\Hsv('300,100,100')
$hsv->toRgb(); // \OzdemirBurak\Iris\Color\Rgb('255,0,255')
echo $hsv; // hsl(300,100%,100%)
```

### RGB

``` php
use OzdemirBurak\Iris\Color\Rgb;

$rgb = new Rgb('rgb(255, 0, 255)'); // same as new Rgb('fuschia');

$rgb->red(); // 255
$rgb->green(); // 0
$rgb->blue(); // 255
$rgb->values(); // [255, 0, 255]
$rgb->toHex(); // \OzdemirBurak\Iris\Color\Hex('ff00ff')
$rgb->toHsl(); // \OzdemirBurak\Iris\Color\Hsl('300,100,50')
$rgb->toHsv(); // \OzdemirBurak\Iris\Color\Hsv('300,100,100')
$rgb->toRgb(); // \OzdemirBurak\Iris\Color\Rgb('255,0,255')
echo $rgb; // rgb(255,0,255)
```

### Color Manipulation

#### Saturation

Saturate or desaturate by a percent.

``` php
use OzdemirBurak\Iris\Color\Rgb;

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

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email mail@burakozdemir.co.uk instead of using the issue tracker.

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
