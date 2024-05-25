<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Colors;

use InvalidArgumentException;
use Tomloprod\Colority\Contracts\ValueColorParser;
use Tomloprod\Colority\Support\Parsers\HslValueColorParser;

final class HslColor extends Color
{
    /**
     * @param  string|array<float>  $valueColor
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string|array $valueColor)
    {
        if (is_array($valueColor)) {
            $valueColor = 'hsl('.implode(',', $valueColor).')';
        }

        $parsedValueColor = self::getParser()->parse($valueColor);

        $this->valueColor = $parsedValueColor;
    }

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
        $results = [];

        preg_match(self::getParser()::getRegex(), $this->valueColor, $results);

        [$hsl, $h, $s, $l] = $results;

        return [(float) $h, (float) $s, (float) $l];
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
}
