<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Colors;

use Tomloprod\Colority\Contracts\HasMeasureUnits;
use Tomloprod\Colority\Contracts\ValueColorParser;
use Tomloprod\Colority\Support\Dtos\ColorComponent;
use Tomloprod\Colority\Support\FloatFormatter;
use Tomloprod\Colority\Support\Parsers\HslValueColorParser;

final class HslColor extends Color implements HasMeasureUnits
{
    public static function getParser(): ValueColorParser
    {
        return new HslValueColorParser();
    }

    public function getValueColorWithMeasureUnits(): string
    {
        [$h, $s, $l] = $this->getArrayValueColor();

        return 'hsl('.$h.'deg,'.$s.'%,'.$l.'%)';
    }

    /**
     * @return array<float> HSL
     */
    public function getArrayValueColor(): array
    {
        return [
            $this->components[0]->value,
            $this->components[1]->value,
            $this->components[2]->value,
        ];
    }

    public function toHex(): HexColor
    {
        // @codeCoverageIgnoreStart
        [$h, $s, $l] = $this->getArrayValueColor();

        $s /= 100;
        $l /= 100;

        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod(($h / 60), 2) - 1));
        $m = $l - $c / 2;

        if ($h >= 0 && $h < 60) {
            $rPrime = $c;
            $gPrime = $x;
            $bPrime = 0;
        } elseif ($h >= 60 && $h < 120) {
            $rPrime = $x;
            $gPrime = $c;
            $bPrime = 0;
        } elseif ($h >= 120 && $h < 180) {
            $rPrime = 0;
            $gPrime = $c;
            $bPrime = $x;
        } elseif ($h >= 180 && $h < 240) {
            $rPrime = 0;
            $gPrime = $x;
            $bPrime = $c;
        } elseif ($h >= 240 && $h < 300) {
            $rPrime = $x;
            $gPrime = 0;
            $bPrime = $c;
        } else {
            $rPrime = $c;
            $gPrime = 0;
            $bPrime = $x;
        }

        $r = round(($rPrime + $m) * 255);
        $g = round(($gPrime + $m) * 255);
        $b = round(($bPrime + $m) * 255);
        // @codeCoverageIgnoreEnd

        return new HexColor(sprintf('#%02X%02X%02X', $r, $g, $b));
    }

    public function toRgb(): RgbColor
    {
        return $this->toHex()->toRgb();
    }

    public function toHsl(): self
    {
        return $this;
    }

    public function toOklch(): OklchColor
    {
        return $this->toRgb()->toOklch();
    }

    /**
     * @param  array<ColorComponent>  $components
     */
    protected function buildCanonical(array $components): string
    {
        return sprintf(
            'hsl(%s,%s,%s)',
            FloatFormatter::asFixedPoint($components[0]->value),
            FloatFormatter::asFixedPoint($components[1]->value),
            FloatFormatter::asFixedPoint($components[2]->value),
        );
    }

    /**
     * @param  array<int|float>  $values  HSL component values [H, S, L].
     */
    protected function arrayToString(array $values): string
    {
        return 'hsl('.implode(',', $values).')';
    }
}
