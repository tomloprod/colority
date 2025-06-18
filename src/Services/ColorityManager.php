<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Services;

use Exception;
use Tomloprod\Colority\Colors\Color;
use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Colors\RgbColor;
use Tomloprod\Colority\Concerns\ExtractsColorsFromImage;
use Tomloprod\Colority\Support\ValueColorParserResolver;

final class ColorityManager
{
    use ExtractsColorsFromImage;

    private static ColorityManager $instance;

    private function __construct() {}

    public function __clone()
    {
        throw new Exception('Cannot clone singleton');
    }

    public function __wakeup()
    {
        throw new Exception('Cannot unserialize singleton');
    }

    /**
     * Get the singleton instance of Colority.
     */
    public static function instance(): self
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Generates a deterministic color from a text string.
     *
     * This method creates a consistent color representation of any given text by using
     * SHA256 hashing to ensure the same text always produces the same color.
     *
     * Behavior modes:
     * - If both `fromColor` and `toColor` are provided: interpolates between
     *   the two colors using the text hash to determine the position in the
     *   gradient (0-1)
     *
     * - If only `fromColor` is provided: generates color variations around the
     *   base color with small random offsets (±30° hue, ±10% saturation/lightness)
     *
     * - If no colors are provided: generates a completely random color based on
     *   the hash
     *
     * @param  string  $text  The input text to convert to a color
     * @param  Color|null  $fromColor  Optional base color for variations or interpolation start
     * @param  Color|null  $toColor  Optional end color for interpolation
     * @return HslColor The generated color in HSL format
     */
    public function textToColor(string $text, ?Color $fromColor = null, ?Color $toColor = null): HslColor
    {
        $hash = hash('sha256', $text);

        if ($fromColor instanceof Color && $toColor instanceof Color) {
            // Interpolar entre dos colores base
            $startHslColor = $fromColor->toHsl();
            $endHslColor = $toColor->toHsl();

            [$startH, $startS, $startL] = $startHslColor->getArrayValueColor();
            [$endH, $endS, $endL] = $endHslColor->getArrayValueColor();

            // Use the hash to determine the position in the gradient (0-1)
            $interpolationFactor = (hexdec(mb_substr($hash, 0, 8)) % 1000) / 1000;

            // Interpolate each HSL component
            $hue = $startH + ($endH - $startH) * $interpolationFactor;
            $saturation = $startS + ($endS - $startS) * $interpolationFactor;
            $lightness = $startL + ($endL - $startL) * $interpolationFactor;

            // Ensure the values are within valid ranges
            $hue = max(0, min(360, $hue));
            $saturation = max(0, min(100, $saturation));
            $lightness = max(0, min(100, $lightness));

        } elseif ($fromColor instanceof Color) {
            // Use only the start color to generate variations within that palette
            $baseHslColor = $fromColor->toHsl();
            [$baseH, $baseS, $baseL] = $baseHslColor->getArrayValueColor();

            // Generate small variations around the base color using the hash
            $hueVariation = (hexdec(mb_substr($hash, 0, 8)) % 61) - 30; // ±30 degrees
            $saturationVariation = (hexdec(mb_substr($hash, 8, 8)) % 21) - 10; // ±10%
            $lightnessVariation = (hexdec(mb_substr($hash, 16, 8)) % 21) - 10; // ±10%

            $hue = max(0, min(360, $baseH + $hueVariation));
            $saturation = max(0, min(100, $baseS + $saturationVariation));
            $lightness = max(0, min(100, $baseL + $lightnessVariation));

        } else {
            $hue = hexdec(mb_substr($hash, 0, 8)) % 361;
            $saturation = hexdec(mb_substr($hash, 8, 8)) % 101;
            $lightness = hexdec(mb_substr($hash, 16, 8)) % 101;
        }

        return $this->fromHsl([$hue, $saturation, $lightness]);
    }

    public function getSimilarColor(Color $color, int $hueRange = 30, int $saturationRange = 10, int $lightnessRange = 10): Color
    {
        [$baseH, $baseS, $baseL] = $color->toHsl()->getArrayValueColor();

        $randomHue = mt_rand(
            max(0, (int) round($baseH - $hueRange)),
            min(360, (int) round($baseH + $hueRange))
        );

        $randomSaturation = mt_rand(
            max(0, (int) round($baseS - $saturationRange, 0)),
            min(100, (int) round($baseS + $saturationRange))
        );

        $randomLightness = mt_rand(
            max(0, (int) round($baseL - $lightnessRange)),
            min(100, (int) round($baseL + $lightnessRange))
        );

        return $this->fromHsl([$randomHue, $randomSaturation, $randomLightness]);
    }

    public function parse(string $valueColor): ?Color
    {
        return (new ValueColorParserResolver())->parse($valueColor);
    }

    public function fromHex(string $hexValue): HexColor
    {
        return new HexColor($hexValue);
    }

    /**
     * @param  string|array<int>  $rgbValue
     */
    public function fromRgb(string|array $rgbValue): RgbColor
    {
        return new RgbColor($rgbValue);
    }

    /**
     * @param  string|array<float>  $hslValue
     */
    public function fromHsl(string|array $hslValue): HslColor
    {
        return new HslColor($hslValue);
    }
}
