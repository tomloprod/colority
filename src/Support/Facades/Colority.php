<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Facades;

use Tomloprod\Colority\Colors\Color;
use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Colors\OklchColor;
use Tomloprod\Colority\Colors\RgbColor;
use Tomloprod\Colority\Services\ColorityManager;

/**
 * @method static Color|null parse(string $valueColor)
 * @method static HexColor fromHex(string|array<int|float> $hexValue)
 * @method static RgbColor fromRgb(string|array<int> $rgbValue)
 * @method static HslColor fromHsl(string|array<float> $hslValue)
 * @method static OklchColor fromOklch(string|array<float> $oklchValue)
 * @method static HslColor textToColor(string $text, ?Color $fromColor = null, ?Color $toColor = null)
 * @method static HslColor random()
 * @method static Color getSimilarColor(Color $color, int $hueRange = 30, int $saturationRange = 10, int $lightnessRange = 10)
 * @method static array<HexColor> gradient(array<Color> $colors, int $steps = 5)
 * @method static Color primary()
 * @method static Color secondary()
 * @method static Color success()
 * @method static Color danger()
 * @method static Color warning()
 * @method static Color info()
 * @method static Color light()
 * @method static Color dark()
 * @method static Color|null color(string $name)
 * @method static void setPrimary(Color|string $color)
 * @method static void setSecondary(Color|string $color)
 * @method static void setSuccess(Color|string $color)
 * @method static void setDanger(Color|string $color)
 * @method static void setWarning(Color|string $color)
 * @method static void setInfo(Color|string $color)
 * @method static void setLight(Color|string $color)
 * @method static void setDark(Color|string $color)
 * @method static void setColor(string $name, Color|string $color)
 * @method static void configure(array<string, Color|string> $palette)
 * @method static void resetPalette()
 * @method static void buildPalette(Color|string $brandColor)
 */
final class Colority
{
    /**
     * @param  array<mixed>  $args
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $instance = ColorityManager::instance();

        return $instance->$method(...$args);
    }
}
