<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Services;

use Exception;
use Tomloprod\Colority\Colors\Color;
use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Colors\RgbColor;
use Tomloprod\Colority\Support\ValueColorParserResolver;

final class ColorityManager
{
    private static ColorityManager $instance;

    private function __construct()
    {
    }

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

    public function textToColor(string $text): HslColor
    {
        $hash = hash('sha256', $text);

        // between 0 and 360deg
        $hue = hexdec(mb_substr($hash, 0, 8)) % 361;

        // between 0 and 100%
        $saturation = hexdec(mb_substr($hash, 8, 8)) % 101;

        // between 0 and 100%
        $lightness = hexdec(mb_substr($hash, 16, 8)) % 101;

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
