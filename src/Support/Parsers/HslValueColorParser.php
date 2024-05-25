<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Parsers;

use InvalidArgumentException;
use Tomloprod\Colority\Contracts\ValueColorParser;

final readonly class HslValueColorParser implements ValueColorParser
{
    /**
     * {@inheritDoc}
     */
    public static function getRegex(): string
    {
        return '/^hsl\(\s*((?:0|[1-9]\d?|[12]\d\d|3[0-5]\d|360)(?:\.\d+)?)\s*,\s*((?:0|100|[1-9]?\d)(?:\.\d+)?)\s*,\s*((?:0|100|[1-9]?\d)(?:\.\d+)?)\s*\)$/';
    }

    /**
     * {@inheritDoc}
     */
    public function parse(string $valueColor): string
    {
        $parsedValueColor = str_replace([' ', 'hsl(', ')', 'deg', '%'], '', $valueColor);

        $parsedValueColor = 'hsl('.$parsedValueColor.')';

        // Validate value color
        $results = [];
        preg_match(self::getRegex(), $parsedValueColor, $results);

        if (count($results) === 0) {
            throw new InvalidArgumentException('Unknown or invalid value color: '.$parsedValueColor);
        }

        return $parsedValueColor;
    }
}
