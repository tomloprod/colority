# API Reference

Complete API documentation for Colority's facades, classes, and methods.

## Quick Reference

| Operation | Method | Example |
|-----------|--------|--------|
| **Parse any color** | `parse()` | `colority()->parse('#FF0000')` |
| **Create from hex** | `fromHex()` | `colority()->fromHex('#51B389')` |
| **Create from RGB** | `fromRgb()` | `colority()->fromRgb('255,0,0')` |
| **Create from HSL** | `fromHsl()` | `colority()->fromHsl([180, 50, 50])` |
| **Create from OKLCH** | `fromOklch()` | `colority()->fromOklch([0.70, 0.11, 163])` |
| **Convert formats** | `toHex()`, `toRgb()`, `toHsl()`, `toOklch()` | `$color->toHex()` |
| **Generate from text** | `textToColor()` | `colority()->textToColor('username')` |
| **Best contrast color** | `getBestForegroundColor()` | `$bg->getBestForegroundColor()` |
| **Get contrast ratio** | `getContrastRatio()` | `$color->getContrastRatio($other)` |
| **Random color** | `random()` | `colority()->random()` |
| **Color gradient** | `gradient()` | `colority()->gradient($colors, 10)` |
| **Image colors** | `getImageColors()` | `colority()->getImageColors($path, 5)` |
| **Most common color** | `getImageMostCommonColor()` | `colority()->getImageMostCommonColor($path)` |
| **Dominant colors** | `getImageDominantColors()` | `colority()->getImageDominantColors($path, 5)` |

## Table of Contents

- [Colority Facade](#colority-facade)
- [Color Classes](#color-classes)
  - [Common Methods](#common-methods)
  - [Type-Specific Methods](#type-specific-methods)
- [Usage Patterns](#usage-patterns)

---

## Colority Facade

`Tomloprod\Colority\Support\Facades\Colority` is a facade that acts as a simplified interface for using all Colority features.

### Methods

#### Color Parsing & Instantiation

```php
// Parse any color format (auto-detect)
Colority::parse(string $valueColor): Color|null

// Instantiate specific color types
Colority::fromHex(string $hexValue): HexColor
Colority::fromRgb(string|array<int> $rgbValue): RgbColor
Colority::fromHsl(string|array<float> $hslValue): HslColor
Colority::fromOklch(string|array<float> $oklchValue): OklchColor
```

**Examples:**

```php
use Tomloprod\Colority\Support\Facades\Colority;
use InvalidArgumentException;

// Auto-detect color format
$color = Colority::parse('#FF0000'); // Returns HexColor
$color = Colority::parse('rgb(255,0,0)'); // Returns RgbColor
$color = Colority::parse('oklch(0.70 0.11 163)'); // Returns OklchColor
$color = Colority::parse('invalid'); // Returns null

// Explicit format with validation
try {
    $hex = Colority::fromHex('#51B389');
    $rgb = Colority::fromRgb('255,255,255');
    $rgb = Colority::fromRgb([255, 255, 255]);
    $hsl = Colority::fromHsl('168.31, 49.58, 46.67');
    $hsl = Colority::fromHsl([168.31, 49.58, 46.67]);
    $oklch = Colority::fromOklch('oklch(0.70 0.11 163)');
    $oklch = Colority::fromOklch([0.70, 0.11, 163]);

} catch (InvalidArgumentException $e) {
    // Handle invalid color format
}
```

#### Color Utilities

```php
// Generate color from text (deterministic)
Colority::textToColor(
    string $text, 
    ?Color $fromColor = null, 
    ?Color $toColor = null
): HslColor

// Get similar color in same palette
Colority::getSimilarColor(
    Color $color, 
    int $hueRange = 30, 
    int $saturationRange = 10, 
    int $lightnessRange = 10
): Color

// Generate random color
Colority::random(): HslColor

// Generate color gradient
Colority::gradient(
    array<Color> $colors, 
    int $steps = 5
): array<HexColor>

// Get representative colors from image
Colority::getImageColors(
    string $imagePath,
    int $desiredNumColors = 5
): array<RgbColor>

// Get the single most common color in an image
Colority::getImageMostCommonColor(
    string $imagePath
): RgbColor

// Get dominant colors from image with frequency metadata
Colority::getImageDominantColors(
    string $imagePath,
    int $desiredNumColors = 5,
    int $similarityThreshold = 50
): array<ImageColorFrequency>
```

**Examples:**

```php
use Tomloprod\Colority\Support\Facades\Colority;

// Deterministic text to color
$userColor = Colority::textToColor('tomloprod');

// Constrain to specific hue range (like brand colors, etc)
$brandColor = Colority::textToColor(
    'username',
    Colority::fromHex('#85d5a4'), // Start point
    Colority::fromHex('#165a59') // End point
);

// Similar color - useful for theme variations
$baseColor = Colority::fromHex('#51B389');
$variation = Colority::getSimilarColor(
    $baseColor, 
    hueRange: 20, // degrees on color wheel
    saturationRange: 10, // percentage points
    lightnessRange: 10 // percentage points
);

// Random color generation
$random = Colority::random();

// Gradient generation
$gradient = Colority::gradient(
    colors: [
        Colority::fromHex('#ff0000'),
        Colority::fromHex('#00ff00'),
        Colority::fromHex('#0000ff'),
    ],
    steps: 10
);
// Returns array of 10 `HexColor` objects

// Extract representative colors from images
$imageColors = Colority::getImageColors(
    imagePath: __DIR__.'/photo.jpg',
    desiredNumColors: 5
);
// Returns array of `RgbColor` objects

// Get the single most common color
$mostCommon = Colority::getImageMostCommonColor(__DIR__.'/photo.jpg');

// Extract dominant colors with frequency metadata
use Tomloprod\Colority\Support\Dtos\ImageColorFrequency;

$dominantColors = Colority::getImageDominantColors(
    imagePath: __DIR__.'/photo.jpg',
    desiredNumColors: 5,
    similarityThreshold: 50 // Filters similar colors (0-441)
);

foreach ($dominantColors as $frequency) {
    $frequency->color; // RgbColor object
    $frequency->percentage; // e.g., 45.2
    $frequency->pixelCount; // e.g., 4520
}
```

---

## Color Classes

All concrete color classes extend the abstract `Color` class:
- `Tomloprod\Colority\Colors\HexColor`
- `Tomloprod\Colority\Colors\HslColor`
- `Tomloprod\Colority\Colors\RgbColor`
- `Tomloprod\Colority\Colors\OklchColor`

### Common Methods

Available on all `Color` objects:

#### Conversion Methods

```php
$color->toHex(): HexColor
$color->toRgb(): RgbColor
$color->toHsl(): HslColor
$color->toOklch(): OklchColor
```

**Example:**
```php
$hex = Colority::fromHex('#51B389');

$rgb = $hex->toRgb(); // Convert to RgbColor
$hsl = $hex->toHsl(); // Convert to HslColor
$oklch = $hex->toOklch(); // Convert to OklchColor
$hex = $hex->toHex(); // It's already an hex color
```

#### Value & Comparison Methods

```php
// Get the color value as string
$color->getValueColor(): string

// Compare two colors for equality
$color->isEqualTo(Color $color): bool

// Check brightness
$color->isDark(): bool
$color->isLight(): bool

// Relative brightness comparison
$color->isDarkerThan(Color $color): bool
$color->isLighterThan(Color $color): bool

// Get relative luminance (0.0 to 1.0) per WCAG 2.0
$color->getLuminance(): float
```

**Examples:**
```php
$hex = Colority::fromHex('#51B389');

// Get value
$value = $hex->getValueColor(); // "#51B389"

// Brightness detection
$isDark = $hex->isDark(); // false
$isLight = $hex->isLight(); // true

// Comparison
$other = Colority::fromHex('#FFFFFF');
$darker = $hex->isDarkerThan($other); // true

// Equality (works across color types)
$rgb = Colority::fromRgb('rgb(255,0,0)');
$hex = Colority::fromHex('#FF0000');
$equal = $hex->isEqualTo($rgb); // true

// Luminance
$luminance = $hex->getLuminance(); // returns float between 0.0 and 1.0
```

#### Contrast & Accessibility Methods

```php
// Get best foreground color for contrast
$color->getBestForegroundColor(array<Color> $candidates = []): Color

// Calculate contrast ratio with another color
$color->getContrastRatio(?Color $foreground = null): float
```

**Examples:**

```php
use Tomloprod\Colority\Support\Facades\Colority;
use Tomloprod\Colority\Support\Algorithms\ContrastRatioScore;

$background = Colority::fromHex('#51B389');

// Automatic contrast selection (black or white)
$foreground = $background->getBestForegroundColor();
echo $foreground->getValueColor(); // "#000000" or "#FFFFFF"

// Choose from custom palette (for example, brand colors)
$foreground = $background->getBestForegroundColor([
    Colority::fromHex('#A63F3F'),
    Colority::fromHex('#3FA684'),
    Colority::fromHex('#6E3FA6'),
]);

// Calculate specific contrast ratios
$contrastVsBlack = $background->getContrastRatio(); // Default: vs black
$contrastVsWhite = $background->getContrastRatio(
    Colority::fromHex('#FFFFFF')
);

// WCAG compliance checking
$ratio = $background->getContrastRatio($foreground);

// Text accessibility (AA level)
$isAANormal = ContrastRatioScore::passesTextAALevel(
    contrastRatio: $ratio,
    largeText: false // Normal text requires 4.5:1
);

$isAALarge = ContrastRatioScore::passesTextAALevel(
    contrastRatio: $ratio,
    largeText: true // Large text requires 3:1
);

// Text accessibility (AAA level - enhanced)
$isAAANormal = ContrastRatioScore::passesTextAAALevel(
    contrastRatio: $ratio,
    largeText: false // Normal text requires 7:1
);

$isAAALarge = ContrastRatioScore::passesTextAAALevel(
    contrastRatio: $ratio,
    largeText: true // Large text requires 4.5:1
);

// UI components (AA level only)
$isUIAccessible = ContrastRatioScore::passesUIAALevel(
    $ratio // UI components require 3:1
);
```

**WCAG 2.0 contrast requirements:**
- **Normal text (< 18pt):** AA requires 4.5:1, AAA requires 7:1
- **Large text (≥ 18pt or 14pt bold):** AA requires 3:1, AAA requires 4.5:1
- **UI components:** AA requires 3:1 (*no AAA level defined*)

### Type-Specific Methods

#### RgbColor, HslColor & OklchColor

These color classes have an additional method to get the value as an array:

```php
$color->getArrayValueColor(): array
```

**Example:**
```php
$rgb = Colority::fromRgb('255,255,255');
$array = $rgb->getArrayValueColor(); // [255, 255, 255]

$hsl = Colority::fromHsl('168.31, 49.58, 46.67');
$array = $hsl->getArrayValueColor(); // [168.31, 49.58, 46.67]

$oklch = Colority::fromOklch('oklch(0.70 0.11 163)');
$array = $oklch->getArrayValueColor(); // [0.70, 0.11, 163.0]
```

#### HslColor Only

```php
// Get HSL value with CSS measurement units
$hslColor->getValueColorWithMeasureUnits(): string
```

**Example:**
```php
$hsl = Colority::fromHsl('hsl(32.4, 60.48, 51.37)');

// With units (CSS-ready)
$withUnits = $hsl->getValueColorWithMeasureUnits(); 
// "hsl(32.4deg,60.48%,51.37%)"

// Without units
$withoutUnits = $hsl->getValueColor(); 
// "hsl(32.4,60.48,51.37)"
```

---

## Color Validation

Each color class has a static parser for validation:

```php
use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\RgbColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Colors\OklchColor;

// Get parser instance
$hexParser = HexColor::getParser();
$rgbParser = RgbColor::getParser();
$hslParser = HslColor::getParser();
$oklchParser = OklchColor::getParser();

// Validate and normalize
try {
    $normalized = $hexParser->parse('#FFF'); // Returns "#FFFFFF"
    $normalized = $hexParser->parse('invalid'); // Throws InvalidArgumentException
    
} catch (InvalidArgumentException $e) {
    // Handle invalid color
}
```

---

## Usage Patterns

### Using the Helper Function

```php
// Recommended for most cases
$hex = colority()->fromHex('#CCC');
$color = colority()->parse('rgb(255,0,0)');
```

### Using the Facade Directly

```php
use Tomloprod\Colority\Support\Facades\Colority;

// Alternative approach
$hex = Colority::fromHex('#CCC');
$color = Colority::parse('rgb(255,0,0)');
```

Both approaches are equivalent.
