<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Colors;

use InvalidArgumentException;
use Tomloprod\Colority\Contracts\ValueColorParser;
use Tomloprod\Colority\Support\ColorSpaceConverter;
use Tomloprod\Colority\Support\Parsers\RgbValueColorParser;

final class RgbColor extends Color
{
    /**
     * @param  string|array<int>  $valueColor
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string|array $valueColor)
    {
        if (is_array($valueColor)) {
            $valueColor = 'rgb('.implode(',', $valueColor).')';
        }

        $parsedValueColor = self::getParser()->parse($valueColor);

        $this->valueColor = $parsedValueColor;
    }

    public static function getParser(): ValueColorParser
    {
        return new RgbValueColorParser();
    }

    /**
     * @return array<int> RGB
     */
    public function getArrayValueColor(): array
    {
        $results = [];

        preg_match(self::getParser()::getRegex(), $this->valueColor, $results);

        [$rgb, $r, $g, $b] = $results;

        return [(int) $r, (int) $g, (int) $b];
    }

    public function toHex(): HexColor
    {
        $rgb = $this->getArrayValueColor();

        $r = dechex((int) ($rgb[0] ?? 0));
        $g = dechex((int) ($rgb[1] ?? 0));
        $b = dechex((int) ($rgb[2] ?? 0));

        $hexValueColor =
            str_pad($r, 2, '0', STR_PAD_LEFT).
            str_pad($g, 2, '0', STR_PAD_LEFT).
            str_pad($b, 2, '0', STR_PAD_LEFT);

        return new HexColor($hexValueColor);
    }

    public function toHsl(): HslColor
    {
        [$r, $g, $b] = $this->getArrayValueColor();

        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);

        $h = $s = $l = ($max + $min) / 2;

        if ($max === $min) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

            switch ($max) {
                case $r:
                    $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                    break;
                case $g:
                    $h = ($b - $r) / $d + 2;
                    break;
                case $b:
                    $h = ($r - $g) / $d + 4;
                    break;
            }

            $h /= 6;
        }

        return new HslColor([
            round($h * 360, 2),
            round($s * 100, 2),
            round($l * 100, 2),
        ]);
    }

    public function toRgb(): self
    {
        return $this;
    }

    public function toOklch(): OklchColor
    {
        [$r, $g, $b] = $this->getArrayValueColor();

        // sRGB -> Linear RGB
        $rLinear = ColorSpaceConverter::srgbToLinear($r / 255);
        $gLinear = ColorSpaceConverter::srgbToLinear($g / 255);
        $bLinear = ColorSpaceConverter::srgbToLinear($b / 255);

        // Linear RGB -> XYZ
        [$x, $y, $z] = ColorSpaceConverter::linearRgbToXyz($rLinear, $gLinear, $bLinear);

        // XYZ -> OKLab
        [$L, $a, $bLab] = ColorSpaceConverter::xyzToOklab($x, $y, $z);

        // OKLab -> OKLCH (cartesian to polar)
        $C = sqrt($a * $a + $bLab * $bLab);
        $H = atan2($bLab, $a) * 180 / M_PI;

        if ($H < 0) {
            $H += 360;
        }

        return new OklchColor([round($L, 6), round($C, 6), round($H, 2)]);
    }
}
