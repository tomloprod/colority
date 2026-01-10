<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Colors;

use InvalidArgumentException;
use Tomloprod\Colority\Contracts\ValueColorParser;
use Tomloprod\Colority\Support\ColorSpaceConverter;
use Tomloprod\Colority\Support\Parsers\OklchValueColorParser;

final class OklchColor extends Color
{
    /**
     * @param  string|array<float>  $valueColor
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string|array $valueColor)
    {
        if (is_array($valueColor)) {
            $valueColor = 'oklch('.implode(' ', $valueColor).')';
        }

        $parsedValueColor = self::getParser()->parse($valueColor);

        $this->valueColor = $parsedValueColor;
    }

    public static function getParser(): ValueColorParser
    {
        return new OklchValueColorParser();
    }

    /**
     * @return array<float> [L, C, H]
     */
    public function getArrayValueColor(): array
    {
        // Extract L, C, H from "oklch(L C H)"
        $value = str_replace(['oklch(', ')'], '', $this->valueColor);
        $parts = explode(' ', trim($value));

        return [(float) $parts[0], (float) $parts[1], (float) $parts[2]];
    }

    /**
     * Convert OKLCH to RGB
     *
     * OKLCH -> OKLab -> XYZ -> Linear RGB -> sRGB
     */
    public function toRgb(): RgbColor
    {
        [$L, $C, $H] = $this->getArrayValueColor();

        // OKLCH -> OKLab (polar to cartesian)
        $hRad = deg2rad($H);
        $a = $C * cos($hRad);
        $b = $C * sin($hRad);

        // OKLab -> XYZ
        [$x, $y, $z] = ColorSpaceConverter::oklabToXyz($L, $a, $b);

        // XYZ -> Linear RGB
        [$rLinear, $gLinear, $bLinear] = ColorSpaceConverter::xyzToLinearRgb($x, $y, $z);

        // Linear RGB -> sRGB (gamma correction)
        $r = ColorSpaceConverter::linearToSrgb($rLinear);
        $g = ColorSpaceConverter::linearToSrgb($gLinear);
        $bSrgb = ColorSpaceConverter::linearToSrgb($bLinear);

        // Clamp to valid range and convert to 0-255
        $r = max(0, min(255, round($r * 255)));
        $g = max(0, min(255, round($g * 255)));
        $bSrgb = max(0, min(255, round($bSrgb * 255)));

        return new RgbColor("rgb({$r},{$g},{$bSrgb})");
    }

    public function toHex(): HexColor
    {
        return $this->toRgb()->toHex();
    }

    public function toHsl(): HslColor
    {
        return $this->toRgb()->toHsl();
    }

    public function toOklch(): self
    {
        return $this;
    }
}
