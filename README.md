<p align="center">
    <img title="Laravel Zero" height="100" src="./docs/colority.png" alt="Colority" />
</p>

<p align="center">
    <p align="center">
        <a href="https://github.com/tomloprod/colority/actions"><img alt="GitHub Workflow Status (master)" src="https://github.com/tomloprod/colority/actions/workflows/tests.yml/badge.svg"></a>
        <a href="https://packagist.org/packages/tomloprod/colority"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/tomloprod/colority"></a>
        <a href="https://packagist.org/packages/tomloprod/colority"><img alt="Latest Version" src="https://img.shields.io/packagist/v/tomloprod/colority"></a>
        <a href="https://packagist.org/packages/tomloprod/colority"><img alt="License" src="https://img.shields.io/packagist/l/tomloprod/colority"></a>
    </p>
</p>

------
## 🎨 **About Colority**

Colority is a lightweight PHP library designed to handle color transformations, validations and manipulations with ease.


It allows you to instantiate concrete objects according to the color format (*RGB, HSL, Hexadecimal*) and convert from one format to another.

Additionally, it lets you check if a **background color meets the WCAG 2.0 accessibility standard** regarding the color contrast ratio in text and UI.

Furthermore, it includes multiple functions such as the following:

- Generate the **best foreground color** (*white, black, or from a user-provided list*) for a background color, ensuring the best possible contrast ratio. (*important for improving text visibility on, for example, colored badges*).
- Generate a **fixed color based on a string.** Useful for generating a color associated with, for example, a username.
- Allows you to obtain a **random color similar to a given color.**

## **✨ Getting Started**

### Instantiating Color objects

You can convert value colors (*strings or, additionally, depending on the color type, arrays*) to specific `Color` objects.

```php
/** @var RgbColor $rgbColor */
$rgbColor = colority()->fromRgb('rgb(255,255,255)');
$rgbColor = colority()->fromRgb('255,255,255'); 
$rgbColor = colority()->fromRgb([255, 255, 255]); 

/** @var HexColor $hexColor */
$hexColor = colority()->fromHex('#51B389');
$hexColor = colority()->fromHex('51B389'); 
$hexColor = colority()->fromHex('#ABC');

/** @var HslColor $hslColor */
$hslColor = colority()->fromRgb('hsl(168.31deg, 49.58%, 46.67%)');
$hslColor = colority()->fromRgb('168.31, 49.58, 46.67'); 
$hslColor = colority()->fromRgb([168.31, 49.58, 46.67]); 
```
If you cannot specify the original format of the value color, you can use the `parse` method. This will detect what type of color it is and instantiate a new object or, if the received string does not match any type of color, it will return `NULL`:
```php
/** @var RgbColor|null $rgbColor */
$rgbColor = colority()->parse('rgb(255,255,255)');

/** @var HexColor|null $hexColor */
$hexColor = colority()->parse('#51B389');

/** @var HslColor|null $hslColor */
$hslColor = colority()->parse('hsl(168.31deg, 49.58%, 46.67%)');
```

### Contrast ratio (*WCAG 2.0 standard*)

When you have the `Color` object, you will be able to use all its methods. Below, we describe two of them related to the contrast ratio.

#### getBestForegroundColor

Returns a `Color` object with the most suitable foreground color (*using the Luminosity Contrast Ratio algorithm*). 

You can pass an array with `Color` objects as a parameter, so it chooses the foreground color with the best contrast ratio. If no parameter is specified, it will default to white or black.

```php
/** @var HexColor $hexColor */
$hexColor = colority()->fromHex('#51B389');

/** @var HexColor $bestForegroundHexColor (black or white) */
$bestForegroundHexColor = $hexColor->getBestForegroundColor();

/** @var HexColor $bestForegroundHexColor (#A63F3F, #3FA684 or #6E3FA6) */
$bestForegroundHexColor = $hexColor->getBestForegroundColor([
    new HexColor('#A63F3F'),
    new HexColor('#3FA684'),
    new HexColor('#6E3FA6'),
]);
```

#### getContrastRatio

Returns the contrast ratio (*higher is better contrast, lower is worse*) between the color invoking this method and the color passed as a parameter. If no color is passed as a parameter, the contrast ratio against black as foreground will be determined.

```php
/** @var HexColor $hexColor */
$hexColor = colority()->fromHex('#51B389');

/** @var float $contrastRatio Contrast ratio with black as the foreground color. */
$contrastRatio = $hexColor->getContrastRatio();

/** @var float $contrastRatio Contrast ratio with #3FA684 as the foreground color. */
$contrastRatio = $hexColor->getContrastRatio(new HexColor('#3FA684'));
```

#### AA & AAA WCAG levels

Below we show you how to check if a contrast ratio meets WCAG AA and AAA levels.

```php
/** @var HexColor $hexColor */
$hexColor = colority()->fromHex('#51B389');

/**
 * AA Level for texts
 */
$passsesAALevelForLargeText = ContrasRatioScore::passesTextAALevel(
    contrastRatio: $hexColor->getContrastRatio(),
    largeText: true
);

$passsesAALevelForNormalText = ContrasRatioScore::passesTextAALevel(
    contrastRatio: $hexColor->getContrastRatio(),
    largeText: false
);

/**
 * AAA Level for texts
 */
$passsesAAALevelForLargeText = ContrasRatioScore::passesTextAAALevel(
    contrastRatio: $hexColor->getContrastRatio(),
    largeText: true
);

$passsesAAALevelForNormalText = ContrasRatioScore::passesTextAAALevel(
    contrastRatio: $hexColor->getContrastRatio(),
    largeText: false
);
/**
 * AA Level for Graphical Objects and User Interface Components
 */
$passsesAALevelForUI = ContrasRatioScore::passesUIAALevel(
    $hexColor->getContrastRatio()
);

```


### Color validation
The concrete `Color` classes have a static method called `getParser()` which returns an instance of `ValueColorParser`.

The `parse` method returns a string with the value color adapted to work correctly with Colority or throws an `InvalidArgumentException` when it's not valid.

```php
/** @var ValueColorParser $hexParser */
$hexParser = HexColor::getParser();

// will throw InvalidArgumentException
$valueColor = $hexParser->parse('Not a valid value color'); 

// will return #FFFFFF
$valueColor = $hexParser->parse('#FFF');
```

You can use the specific parser for any type of color:

```php
$hslParser = HslColor::getParser();

$rgbParser = RgbColor::getParser();

$hexParser = HexColor::getParser();
```

### Color conversion
Colority allows you to convert a Color object to any other `Color` object of the desired format.
```php
/** @var HexColor|null $hexColor */
$hexColor = colority()->fromHex('#51B389');

/** @var HexColor $hexColor */
$hexColor = $hexColor->toHex();

/** @var RgbColor $rgbColor */
$rgbColor = $hexColor->toRgb();

/** @var HslColor $hslColor */
$hslColor = $hexColor->toHsl();
```

### Color utilities

#### textToColor

Generate a fixed color based on a string.

```php
/** @var HslColor $hslColor */
$hslColor = colority()->textToColor("Hi, I'm Tomás");
```
> **🧙 Advise** 
> Useful for generating a color associated with, for example, a username, mail address, etc, since a string will always return the same color.

#### getSimilarColor
Allows you to obtain a random color similar (*in the same color palette*) to a given color.

```php
/** @var HexColor|null $hexColor */
$hexColor = colority()->fromHex('#51B389');

/** @var HexColor|null $similarHexColor */
$similarHexColor = colority()->getSimilarColor($hexColor);
```

### Ways of using Colority
You can use Colority either with the aliases `colority()` 
```php
/** @var HexColor $hexColor */
$hexColor = colority()->fromHex('#CCC');
```

or by directly invoking the static methods of the `Colority` facade:

```php
/** @var HexColor $hexColor */
$hexColor = Colority::fromHex('#CCC');
```
You decide how to use it 🙂


## **🧱 Architecture**
Colority is composed of several types of elements. Below are some features of each of these elements.

### `Colority`

`Tomloprod\Colority\Support\Facades\Colority` is a facade that acts as a simplified interface for using the rest of the Colority elements.

#### Methods
```php

Colority::parse(string $valueColor): Color|null

Colority::fromHex(string $hexValue): HexColor

Colority::fromRgb(string|array<int> $rgbValue): RgbColor

Colority::fromHsl(string|array<float> $hslValue): HslColor

Colority::textToColor(string $text): HslColor

Colority::getSimilarColor(Color $color, int $hueRange = 30, int $saturationRange = 10, int $lightnessRange = 10): Color
```

### `Color`
All concrete color classes extend the abstract class `Color`. Concrete color classes:
- `Tomloprod\Colority\Colors\HexColor`
- `Tomloprod\Colority\Colors\HslColor`
- `Tomloprod\Colority\Colors\RgbColor`

#### Methods

```php
/** @var HexColor $hexColor */
$hexColor = Colority::fromHex('#CCCCCC');

$hexColor->toHex(): HexColor;
$hexColor->toRgb(): RgbColor;
$hexColor->toHsl(): HslColor;

// Returns the value color in string format. Example: #CCCCCC
$hexColor->getValueColor(): string;
```

For the `HslColor` and `RgbColor` objects, you also have a method `getArrayValueColor` that will return the value color in array format:

```php
/** @var RgbColor $rgbColor */
$rgbColor = Colority::fromRgb('255,255,255');

/** @var array $arrayValueColor [255,255,255] */
$arrayValueColor = $rgbColor->getArrayValueColor();
```

On the other hand, the `HslColor` object has an additional method called `getValueColorWithMeasureUnits`, which returns the value color, but with units of measurement (*useful for, for example, using it in a CSS style*):
```php
/** @var HslColor $hslColor */
$hslColor = Colority::fromHsl('hsl(32.4, 60.48, 51.37)');

/** @var string $valueColorWithMeasureUnits hsl(32.4deg,60.48%,51.37%) */
$valueColorWithMeasureUnits = $hslColor->getValueColorWithMeasureUnits();

/** @var string $valueColor hsl(32.4,60.48,51.37) */
$valueColor = $hslColor->getValueColor(): string;

```


## **🚀 Installation & Requirements**

> **Requires [PHP 8.2+](https://php.net/releases/)**

You may use [Composer](https://getcomposer.org) to install Colority into your PHP project:

```bash
composer require tomloprod/colority
```

## **🧑‍🤝‍🧑 Contributing**

Contributions are welcome, and are accepted via pull requests.
Please [review these guidelines](./CONTRIBUTING.md) before submitting any pull requests.

------

**Colority** was created by **[Tomás López](https://twitter.com/tomloprod)** and open-sourced under the **[MIT license](https://opensource.org/licenses/MIT)**.
