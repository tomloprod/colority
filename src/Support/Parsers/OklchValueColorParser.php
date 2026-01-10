<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Parsers;

use InvalidArgumentException;
use Tomloprod\Colority\Contracts\ValueColorParser;

final readonly class OklchValueColorParser implements ValueColorParser
{
    /**
     * {@inheritDoc}
     */
    public static function getRegex(): string
    {
        return '/^oklch\(\s*((?:0|1|0?\.\d+)|(?:0|100|[1-9]?\d)(?:\.\d+)?%)\s+((?:0|[01](?:\.\d+)?))\s+((?:0|[1-9]\d?|[12]\d\d|3[0-5]\d|360)(?:\.\d+)?)\s*\)$/';
    }

    /**
     * {@inheritDoc}
     */
    public function parse(string $valueColor): string
    {
        $parsedValueColor = str_replace(['deg', '%'], '', $valueColor);
        $parsedValueColor = preg_replace('/\s+/', ' ', $parsedValueColor);
        $parsedValueColor = trim((string) $parsedValueColor);

        // Validate value color
        $results = [];
        preg_match(self::getRegex(), $parsedValueColor, $results);

        if ($results === []) {
            throw new InvalidArgumentException('Unknown or invalid value color: '.$parsedValueColor);
        }

        return $parsedValueColor;
    }
}
