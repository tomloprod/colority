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
 * @method static HexColor fromHex(string $hexValue)
 * @method static RgbColor fromRgb(string|array<int> $rgbValue)
 * @method static HslColor fromHsl(string|array<float> $hslValue)
 * @method static OklchColor fromOklch(string|array<float> $oklchValue)
 * @method static HslColor textToColor(string $text, ?Color $fromColor = null, ?Color $toColor = null)
 * @method static HslColor random()
 * @method static Color getSimilarColor(Color $color, int $hueRange = 30, int $saturationRange = 10, int $lightnessRange = 10)
 * @method static array<HexColor> gradient(array<Color> $colors, int $steps = 5)
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
