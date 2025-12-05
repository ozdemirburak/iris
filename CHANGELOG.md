# Changelog

All Notable changes to `iris` will be documented in this file.

## 2025-12-05

### Added

- `gradient()` method to generate color scales between two or more colors (#51)
  - Supports two-color gradients: `$colorA->gradient($colorB, $steps)`
  - Supports multi-color gradients with pivot colors: `$colorA->gradient([$colorB, $colorC], $steps)`
  - Works with all color types (Hex, RGB, HSL, HSV, CMYK, OKLCH, etc.)
  - Returns array of colors matching the starting color type

### Fixed

- CMYK to RGB conversion now rounds values to produce valid integers (#52)
  - Previously, CMYK values that didn't produce clean 0 or 255 RGB values would fail validation

## 2025-12-02

### Added

- OKLCH color space support - perceptually uniform color representation
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

## 2022-02-08

- Remove PHP 7 support, will only support PHP 8
- Added type hinting and return type declarations
- Made BaseColor::back() and BaseColor::getColorModelName() public @rv1971
- Use non-locale aware output for the alpha value @Jako
- Updated .gitignore, and some code refactoring @kudashevs

## 2021-10-05

- Added Hexa support.

## 2021-01-31

- Fix HSL to HSV conversion.

## 2020-12-06

- PHP 8.0 support.

## 2019-10-12

- Added Factory class to attempt to guess the color.

## 2018-10-02

- Added shade method.

## 2018-10-01

- Added tint method.

## 2018-04-08

- Added isLight and isDark methods.

## 2017-10-03

- Added HSLA and RGBA support.

## 2017-05-30

- Initial release.
- Hex, HSL, HSV and RGB support.
- Saturate, desaturate, grayscale, lighten, darken, brighten, spin and mix operations.
