<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Parsers;

use InvalidArgumentException;
use Tomloprod\Colority\Contracts\ValueColorParser;

final readonly class HexValueColorParser implements ValueColorParser
{
    /**
     * {@inheritDoc}
     */
    public static function getRegex(): string
    {
        return '/^#(?:[0-9a-fA-F]{3}){1,2}$/';
    }

    /**
     * {@inheritDoc}
     */
    public function parse(string $valueColor): string
    {
        $hex = str_replace([' ', '#'], '', $valueColor);

        if (mb_strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $parsedValueColor = '#'.$hex;

        // Validate value color
        $results = [];
        preg_match(self::getRegex(), $parsedValueColor, $results);

        if ($results === []) {
            throw new InvalidArgumentException('Unknown or invalid value color');
        }

        return $parsedValueColor;
    }
}
