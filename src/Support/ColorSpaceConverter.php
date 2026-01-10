<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support;

/**
 * Utility class for converting between color spaces
 *
 * Provides transformations between different color representations:
 *
 * - sRGB <--> Linear RGB (gamma correction)
 * - Linear RGB <--> XYZ (CIE XYZ color space)
 * - XYZ <--> OKLab (Oklab perceptual color space)
 */
final class ColorSpaceConverter
{
    /**
     * Convert sRGB to Linear RGB (gamma expansion)
     */
    public static function srgbToLinear(float $channel): float
    {
        if ($channel <= 0.04045) {
            return $channel / 12.92;
        }

        return (($channel + 0.055) / 1.055) ** 2.4;
    }

    /**
     * Convert Linear RGB to sRGB (gamma compression)
     */
    public static function linearToSrgb(float $channel): float
    {
        if ($channel <= 0.0031308) {
            return 12.92 * $channel;
        }

        return 1.055 * $channel ** (1 / 2.4) - 0.055;
    }

    /**
     * Convert Linear RGB to XYZ (D65 illuminant)
     *
     * @return array<float> [X, Y, Z]
     */
    public static function linearRgbToXyz(float $r, float $g, float $b): array
    {
        // Linear sRGB -> XYZ D65 transformation matrix
        $x = 0.4124564 * $r + 0.3575761 * $g + 0.1804375 * $b;
        $y = 0.2126729 * $r + 0.7151522 * $g + 0.0721750 * $b;
        $z = 0.0193339 * $r + 0.1191920 * $g + 0.9503041 * $b;

        return [$x, $y, $z];
    }

    /**
     * Convert XYZ to Linear RGB (D65 illuminant)
     *
     * @return array<float> [R, G, B]
     */
    public static function xyzToLinearRgb(float $x, float $y, float $z): array
    {
        // XYZ D65 -> Linear sRGB transformation matrix
        $r = +3.2404542 * $x - 1.5371385 * $y - 0.4985314 * $z;
        $g = -0.9692660 * $x + 1.8760108 * $y + 0.0415560 * $z;
        $b = +0.0556434 * $x - 0.2040259 * $y + 1.0572252 * $z;

        return [$r, $g, $b];
    }

    /**
     * Convert XYZ to OKLab
     *
     * @return array<float> [L, a, b]
     */
    public static function xyzToOklab(float $x, float $y, float $z): array
    {
        // XYZ -> LMS transformation
        $l = 0.8189330101 * $x + 0.3618667424 * $y - 0.1288597137 * $z;
        $m = 0.0329845436 * $x + 0.9293118715 * $y + 0.0361456387 * $z;
        $s = 0.0482003018 * $x + 0.2643662691 * $y + 0.6338517070 * $z;

        // Apply cube root
        $lCubedRoot = max(0, $l) ** (1 / 3);
        $mCubedRoot = max(0, $m) ** (1 / 3);
        $sCubedRoot = max(0, $s) ** (1 / 3);

        // LMS -> OKLab transformation
        $L = 0.2104542553 * $lCubedRoot + 0.7936177850 * $mCubedRoot - 0.0040720468 * $sCubedRoot;
        $a = 1.9779984951 * $lCubedRoot - 2.4285922050 * $mCubedRoot + 0.4505937099 * $sCubedRoot;
        $b = 0.0259040371 * $lCubedRoot + 0.7827717662 * $mCubedRoot - 0.8086757660 * $sCubedRoot;

        return [$L, $a, $b];
    }

    /**
     * Convert OKLab to XYZ
     *
     * @return array<float> [X, Y, Z]
     */
    public static function oklabToXyz(float $L, float $a, float $b): array
    {
        // OKLab -> LMS (inverse transformation)
        $lCubedRoot = $L + 0.3963377774 * $a + 0.2158037573 * $b;
        $mCubedRoot = $L - 0.1055613458 * $a - 0.0638541728 * $b;
        $sCubedRoot = $L - 0.0894841775 * $a - 1.2914855480 * $b;

        // Cube to get LMS (inverse of cube root)
        $l = $lCubedRoot * $lCubedRoot * $lCubedRoot;
        $m = $mCubedRoot * $mCubedRoot * $mCubedRoot;
        $s = $sCubedRoot * $sCubedRoot * $sCubedRoot;

        // LMS -> XYZ (inverse transformation matrix)
        $x = +1.2270138511 * $l - 0.5577999807 * $m + 0.2812561490 * $s;
        $y = -0.0405801784 * $l + 1.1122568696 * $m - 0.0716766787 * $s;
        $z = -0.0763812845 * $l - 0.4214819784 * $m + 1.5861632204 * $s;

        return [$x, $y, $z];
    }
}
