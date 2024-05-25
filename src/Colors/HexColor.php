<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Colors;

use InvalidArgumentException;
use Tomloprod\Colority\Contracts\ValueColorParser;
use Tomloprod\Colority\Support\Parsers\HexValueColorParser;

final class HexColor extends Color
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $valueColor)
    {
        $parsedValueColor = self::getParser()->parse($valueColor);

        $this->valueColor = $parsedValueColor;
    }

    public static function getParser(): ValueColorParser
    {
        return new HexValueColorParser();
    }

    public function toRgb(): RgbColor
    {
        $hex = str_replace('#', '', $this->valueColor);

        $r = hexdec(mb_substr($hex, 0, 2));
        $g = hexdec(mb_substr($hex, 2, 2));
        $b = hexdec(mb_substr($hex, 4, 2));

        return new RgbColor('rgb('.$r.','.$g.','.$b.')');
    }

    public function toHsl(): HslColor
    {
        return $this->toRgb()->toHsl();
    }

    public function toHex(): self
    {
        return $this;
    }
}
