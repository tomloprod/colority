<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Parsers;

use InvalidArgumentException;
use Tomloprod\Colority\Support\FloatFormatter;

final class HslValueColorParser extends AbstractValueColorParser
{
    public static function getRegex(): string
    {
        return '/^hsl\(\s*((?:0|[1-9]\d?|[12]\d\d|3[0-5]\d|360)(?:\.\d+)?)\s*,\s*((?:0|100|[1-9]?\d)(?:\.\d+)?)\s*,\s*((?:0|100|[1-9]?\d)(?:\.\d+)?)\s*\)$/';
    }

    public function parse(string $valueColor): array
    {
        $stripped = str_replace(['hsl(', ')'], '', $valueColor);
        $parts = $this->splitComponents($stripped);

        if (count($parts) !== 3) {
            throw new InvalidArgumentException('Unknown or invalid value color: '.$valueColor);
        }

        $components = [
            $this->parseComponent($parts[0], ['deg' => 1.0]),
            $this->parseComponent($parts[1], ['%' => 1.0]),
            $this->parseComponent($parts[2], ['%' => 1.0]),
        ];

        $canonical = sprintf(
            'hsl(%s,%s,%s)',
            FloatFormatter::asFixedPoint($components[0]->value),
            FloatFormatter::asFixedPoint($components[1]->value),
            FloatFormatter::asFixedPoint($components[2]->value),
        );

        if (preg_match(self::getRegex(), $canonical) !== 1) {
            throw new InvalidArgumentException('Unknown or invalid value color: '.$canonical);
        }

        return $components;
    }
}
