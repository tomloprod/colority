<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Colors;

use Tomloprod\Colority\Contracts\HasMeasureUnits;
use Tomloprod\Colority\Contracts\ValueColorParser;
use Tomloprod\Colority\Support\ColorSpaceConverter;
use Tomloprod\Colority\Support\Dtos\ColorComponent;
use Tomloprod\Colority\Support\FloatFormatter;
use Tomloprod\Colority\Support\Parsers\OklchValueColorParser;

final class OklchColor extends Color implements HasMeasureUnits
{
    public static function getParser(): ValueColorParser
    {
        return new OklchValueColorParser();
    }

    /**
     * @return array<float> [L, C, H]
     */
    public function getArrayValueColor(): array
    {
        return [
            $this->components[0]->value,
            $this->components[1]->value,
            $this->components[2]->value,
        ];
    }

    /**
     * CSS-ready OKLCH form with conventional unit suffixes:
     * L as percentage (0-100%), C as percentage of the 0.4 reference (0-100%),
     * and H as degrees.
     */
    public function getValueColorWithMeasureUnits(): string
    {
        [$L, $C, $H] = $this->getArrayValueColor();

        return sprintf(
            'oklch(%s%% %s%% %sdeg)',
            FloatFormatter::asFixedPoint($L * 100),
            FloatFormatter::asFixedPoint($C / 0.4 * 100),
            FloatFormatter::asFixedPoint($H),
        );
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

        return new RgbColor(sprintf('rgb(%s,%s,%s)', $r, $g, $bSrgb));
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

    /**
     * @param  array<ColorComponent>  $components
     */
    protected function buildCanonical(array $components): string
    {
        return sprintf(
            'oklch(%s %s %s)',
            FloatFormatter::asFixedPoint($components[0]->value),
            FloatFormatter::asFixedPoint($components[1]->value),
            FloatFormatter::asFixedPoint($components[2]->value),
        );
    }

    /**
     * @param  array<int|float>  $values  OKLCH component values [L, C, H].
     */
    protected function arrayToString(array $values): string
    {
        return sprintf(
            'oklch(%s %s %s)',
            FloatFormatter::asFixedPoint((float) ($values[0] ?? 0)),
            FloatFormatter::asFixedPoint((float) ($values[1] ?? 0)),
            FloatFormatter::asFixedPoint((float) ($values[2] ?? 0)),
        );
    }
}
