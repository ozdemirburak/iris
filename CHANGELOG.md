# Changelog

All Notable changes to `iris` will be documented in this file.

## 5.0.0 - 2026-10-23

### Added

- CSS Color Level 4 syntax for all color functions: space-separated values (`rgb(10 20 30)`), the alpha after a `/` (`rgb(10 20 30 / 50%)`), the alpha as a percentage, RGB values as percentages, the hue units `deg`, `grad`, `rad` and `turn`, any hue angle (it wraps around 360, e.g. `hsl(-120 …)`), a `+` sign, and the `none` keyword
- `rgb()` and `hsl()` with an alpha: `Factory::init()` returns `Rgba` and `Hsla`, and the `Rgba` and `Hsla` classes accept them
- Alpha for `Oklch`: `oklch(62.8% 0.2577 29.23 / 0.5)` keeps the alpha, `alpha()` reads and sets it, and the string form writes it when it is below 1
- The `transparent` keyword in `Rgba`, `Hsla`, `Hexa` and `Factory::init()`
- 3- and 4-digit hex in `Hexa`, and `#` with 4 digits in `Factory::init()`
- Decimal RGB values (rounded), decimal CMYK values, numbers with an exponent (`1e2`), line breaks inside a color, and whitespace around a color
- `Oklch` chroma as a percentage (100% is 0.4)
- `Rgba::background()` without an argument returns a copy of the background
- WCAG 2 contrast: `luminance()`, `contrast($background)`, `isReadable($background, $ratio = 4.5)` and `mostReadable($candidates)`
- Color difference: `distance($other)` (deltaE OK, CSS Color 4) and `equals($other, $tolerance = 0.0)` to compare colors in any model
- `toName()` (e.g. `tomato`), `invert()` and `complement()`
- `json_encode()` of a color gives its string form (`BaseColor` implements `JsonSerializable`)
- `Factory::tryInit()` returns null for a string that is not a color, and `Factory::isValid()`
- `Helpers\ColorString`, one parser for all color functions
- PHPStan static analysis at the max level in CI (`composer analyse`), with no baseline, and `composer check` to run PHPStan and the tests
- A CI job with the lowest dependency versions, to prove the version constraints in `composer.json`
- An "Upgrading from 4.x" section in the README
- The test suite fails on PHP deprecations, notices and warnings, risky tests and PHPUnit deprecations; 161 tests became 287

### Changed

- **Breaking:** Minimum PHP version is now 8.4, the oldest PHP version with active support
- Development dependencies at their latest versions: PHPUnit ^13.4 and PHPStan ^2.3; CI uses `actions/checkout` v7
- **Breaking:** All manipulation methods and all conversions return a new object, also a conversion to the same model (e.g. `Hex::toHex()`); before, some manipulations changed the original `Hsl`, `Rgb`, `Rgba` and `Hsla` object, and a conversion to the same model returned the object itself
- **Breaking:** The setters of `Rgb`, `Rgba`, `Hsl`, `Hsla`, `Hsv`, `Cmyk` and `Oklch` clamp a value that is out of range, e.g. `red(300)` sets 255 and `saturation(150)` sets 100, and the hue wraps around 360, e.g. `hue(400)` sets 40; before, these values were ignored. The setters of `Hex` and `Hexa` take hex digits, as before
- **Breaking:** A setter that gets a value that is not a number (or `NAN` or `INF`) ignores it and returns the color; before, `hue('abc')` returned the hue, and `alpha('abc')` set 0. The `Oklch` setters take only numbers
- **Breaking:** Manipulation methods on `Rgba`, `Hsla`, `Hexa` and `Oklch` keep the alpha (and the `Rgba` background); before, `Rgba` and `Hsla` were blended onto white and the alpha was lost
- **Breaking:** `mix()`, `tint()`, `shade()` and `gradient()` mix the alpha of both colors, and weight the channels by alpha like less.js and Sass: `rgba(255,0,0,1)` mixed 50% with `rgba(0,0,255,0)` gives `rgba(255,0,0,0.5)`; an opaque color mixed with a transparent one is blended onto white
- **Breaking:** Blending onto the background (e.g. `Rgba::toRgb()` and `toHex()`) rounds each channel; before, it rounded down, so 65% of the results were 1 too dark, e.g. `rgba(0,0,0,0.3)` gives `rgb(179,179,179)` and not `rgb(178,178,178)`
- **Breaking:** `fade()`, `fadeIn()` and `fadeOut()` return `Hexa` for `Hex` and `Hexa`, and `Oklch` for `Oklch`; before, they returned `Rgba`
- **Breaking:** `Oklch` keeps the alpha of its input, so `oklch(62.8% 0.2577 29.23 / 0.5)->toHex()` is blended onto white and gives `#ff8080` (4.x ignored the alpha and gave `#ff0000`); an alpha above 1 is rejected; `Oklch::values()` includes the alpha
- **Breaking:** `Rgba`, `Hsla` and `Hexa` keep their RGB and alpha when they convert to `Oklch`; before, they were blended onto white
- **Breaking:** Conversions to HSV and CMYK keep one decimal, like RGB to HSL, so the string form can change, e.g. `hsv(303.5,96.7%,96.5%)` and `cmyk(0,60.6,84.6,3.5)`; round trips through HSV and CMYK give the same color; `Hsv::value()` returns a float
- **Breaking:** `Oklch` reads a lightness without `%` that is not greater than 1 as a number from 0 to 1 (CSS Color 4), so `oklch(0.7 0.1 120)` is 70%; a larger number is still a percentage
- **Breaking:** `Factory::init()` throws `InvalidColorException` for an empty string and for color functions it does not support (e.g. `hwb()`), and reads 4 hex digits only with a `#`, so words like `cafe` are not colors; before, it threw `AmbiguousColorString`
- **Breaking:** `Hex` and `Hexa` accept only one leading `#`
- **Breaking:** `Hex` and `Hexa` use the new `HexTrait` (hex string channels), and `Rgb` and `Rgba` use `RgbTrait` (int channels)
- **Breaking:** Stricter native types: `BaseColor` declares an abstract `toHexa()`; the manipulation methods return `static`; `fade()`, `fadeIn()`, `fadeOut()`, `tint()` and `shade()` take a `float`; `isLight()` and `isDark()` return `bool`; `back()` returns `BaseColor`; `clone()` returns `static`; `getColorModelName()` returns `string`; `Rgb::red()` returns `int` and `Hex::red()` returns `string` (also green and blue); all properties are typed
- **Breaking:** For subclasses: new public methods in `BaseColor` (see Added) and new protected methods (`alphaRaw()`, `opaqueRgb()`, `opaqueHsl()`, `toAlphaModel()`, `limit()`, `wrapHue()`, `isNoChange()`, `gradientStop()`, `linearize()`, `rgbToOklab()`), and `HsTrait` requires `lightnessOrValue()`
- Operations with an amount of 0 (e.g. `spin(0)`, `lighten(0)`, `mix($color, 0)`) give the same color, also for `Hsv`, `Cmyk` and `Oklch`
- Manipulations of `Hsla` and `Hsv` no longer go through RGB, so they are more precise, e.g. `hsla(203,45%,43%,1)->saturate(20)` gives `hsla(203,65%,43%,1)` and not `hsla(203,65.2%,42.9%,1)`
- `Oklch::fromRgb()` keeps the exact values; the getters and the string form round them, as before
- The first and the last color of a `gradient()` are exactly the given colors; each color keeps its own alpha when the first color has an alpha channel
- `InvalidColorException` and `AmbiguousColorString` extend `\InvalidArgumentException`; they are still instances of `\Exception`
- Faster: `new Rgb()` is about 4 times faster for the `r,g,b` strings that the conversions create, and `Rgba` creates its default white background only once
- The Composer package no longer contains the tests and the CI files (`.gitattributes`)

### Fixed

- PHP 8.5 deprecation `Non-canonical cast (double)` in `Rgba` and `Hsla`
- `Rgba` setters (`red()`, `green()`, `blue()`) read integers as hex strings, so `red(100)` was ignored and `red(5)` set 55
- `saturate()`, `desaturate()`, `lighten()`, `darken()`, `brighten()`, `spin()`, `mix()`, `tint()`, `shade()` and `gradient()` on `Rgba`, `Hsla` and `Hexa` dropped the alpha channel (`Rgba` and `Hsla` were also blended onto white)
- Color names in `Hsl`, `Hsla`, `Hsv` and `Cmyk` came from rounded tables and were up to 2 units wrong, e.g. `new Hsl('coral')` gave `#ff8052` and not `#ff7f50`; they now come from the exact hex value
- `fade()` on `Hsla` returned an `Rgba`
- `spin()` triggered `Implicit conversion from float to int` deprecations and lost the decimals of the hue
- `mix()`, `tint()` and `shade()` with a percent outside 0-100 dropped color channels; the percent is now clamped
- `gradient()` with an empty color array threw `DivisionByZeroError`, and with fewer steps than colors it lost the last color
- `Factory::init()` removed all spaces, so the space-separated `oklch()` syntax (also the output of `Oklch::__toString()`) failed
- `Factory::init()` read `'0,0,0,5'` (CMYK with a one-digit black) as RGBA and failed, and merged `'10 20 30'` into the hex `#102030`
- The parser could not read very small values in the form that PHP prints them (e.g. a hue of `1.0E-5`), so `fade()`, `darken()` and other operations threw an exception
- Float noise in the string form, e.g. `hsl(0,50%,0.009999999999998%)` after `darken(49.99)`
- `Hsv` value with decimals (e.g. `hsv(120,50%,50.5%)`) triggered deprecations and was truncated
- `Hex` setters with a float triggered deprecations
- Alpha values without a leading zero (e.g. `rgba(0,0,0,.5)`) were rejected, and `rgb(255.4 0 0)` was rejected although it rounds to 255
- Short `Rgba`/`Hsla` strings such as `'1,2'` triggered `Undefined array key` warnings
- `Rgba::toHsla()` threw for very small alpha values (`1.0E-5`); `Hsla::toRgba()` rounded alpha to 2 decimals
- `Rgba::toRgb()` compositing used the rounded alpha
- `back()` could change the color that it was called on, and `Rgba::background()` shared its object with the caller
- `Oklch::fromRgb()` rounded the values it stored, so 0.08% of the colors did not convert back exactly, and some got the hue 360
- Malformed input was accepted, e.g. `oklch(1.2.3 0.1 20)`, `hsl(120,1%00,50)`, `##fff` and `rgba((10,20,30,0.5`
- The form feed (`\f`) was not whitespace in `Factory::init()` and for color names
- Gray colors converted to `Oklch` got a random hue (89.88); it is now 0

### Removed

- Support for PHP 8.1, 8.2 and 8.3
- The protected `$castsInteger` property and the protected methods `AlphaTrait::fixPrecision()`, `HsTrait::validationRules()`, `RgbTrait::validateAndSet()` and `CmykTrait::validateAndSet()`; the parsing is in `Helpers\ColorString`

## 4.1.2 - 2026-08-03

### Fixed

- `alpha()` clamps a negative value to 0; before, it passed through and gave a string that the constructor rejects (#53)

## 4.1.1 - 2026-01-07

### Fixed

- Incorrect `@param` type of `alpha()` (#52)

## 4.1.0 - 2025-12-05

### Added

- `gradient()` method to generate color scales between two or more colors (#51)
  - Supports two-color gradients: `$colorA->gradient($colorB, $steps)`
  - Supports multicolor gradients with pivot colors: `$colorA->gradient([$colorB, $colorC], $steps)`
  - Works with all color types (Hex, RGB, HSL, HSV, CMYK, OKLCH, etc.)
  - Returns array of colors matching the starting color type

### Fixed

- CMYK to RGB conversion now rounds values to produce valid integers
  - Previously, CMYK values that didn't produce clean 0 or 255 RGB values would fail validation

## 4.0.1 - 2025-12-02

### Fixed

- OKLCH parsing accepts both the comma-separated and the space-separated format

## 4.0.0 - 2025-12-02

### Added

- OKLCH color space support - perceptually uniform color representation
- CMYK color space support: the `Cmyk` class, `toCmyk()` on all color classes, and CMYK detection in `Factory::init()` (NikarashiHatsu)
- `toOklch()` method on all color classes
- `Oklch::fromRgb()` static factory method for RGB to OKLCH conversion
- `clone()` method for color objects
- `alphaRaw()` protected method for internal alpha precision handling
- Comprehensive alpha conversion tests

### Changed

- **Breaking:** Minimum PHP version is now 8.1
- **Breaking:** PHPUnit upgraded to ^10.0|^11.0
- GitHub Actions workflow updated for PHP 8.1, 8.2, 8.3, and 8.4

### Fixed

- RGBA to Hexa conversion now preserves original RGB values instead of alpha-compositing (#48)
- HSLA to Hexa conversion now preserves original RGB values (#48)
- Alpha channel precision in conversions (#37)
- Alpha float to hex conversion now properly zero-pads single digit hex values

### Removed

- Travis CI configuration (migrated to GitHub Actions)
- EditorConfig file
- PHP CodeSniffer dependency

## 3.1.1 - 2023-02-23

- The hue, saturation and lightness of HSL and HSV are floats, to avoid implicit conversion warnings @jaulz

## 3.1.0 - 2023-02-08

- `saturate()`, `desaturate()`, `brighten()`, `lighten()` and `darken()` accept a float percentage @jaulz

## 2.5.0 - 2022-02-13

Release for PHP 7.3 and 7.4 on the 2.x branch.

- Added `BaseColor::mixInHsv()` @rv1971
- PHP 7.3 support @rv1971

## 3.0.0 - 2022-02-08

- Remove PHP 7 support, will only support PHP 8
- Added type hinting and return type declarations
- Made BaseColor::back() and BaseColor::getColorModelName() public @rv1971
- Use non-locale aware output for the alpha value @Jako
- Updated .gitignore, and some code refactoring @kudashevs

## 2.4.0 - 2021-10-05

- Added Hexa support @amnuts

## 2.3.1 - 2021-01-31

- Fix HSL to HSV conversion (#27)

## 2.3.0 - 2020-12-06

- PHP 8.0 support.

## 2.2.0 - 2020-10-11

- Added fade method (Ognjen)
- Added fadeIn and fadeOut methods (Kevin Hamer)

## 2.1.0 - 2020-04-14

- Removed PHP 7.1 support
- The alpha value can have more than two decimals
- Fixed the namespace casing in docblocks

## 2.0.1 - 2019-10-19

- Added the full list of CSS named colors @amnuts

## 2.0.0 - 2019-10-12

- Added Factory class to attempt to guess the color @amnuts
- Removed support for PHP 7.0 and below

## 1.2.1 - 2018-10-08

- Added tint method.
- Added shade method.

## 1.2.0 - 2018-04-08

- Added isLight and isDark methods.

## 1.1.1 - 2017-10-03

- Added return types and docblocks
- Fixed the conversion of HSLA to HSLA
- Dropped PHP 5.5 support
- More tests and a better code coverage

## 1.1.0 - 2017-10-03

- Added HSLA and RGBA support.

## 1.0.0 - 2017-05-30

- Initial release.
- Hex, HSL, HSV and RGB support.
- Saturate, desaturate, grayscale, lighten, darken, brighten, spin and mix operations.
