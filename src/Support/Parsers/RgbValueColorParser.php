<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Parsers;

use InvalidArgumentException;
use Tomloprod\Colority\Contracts\ValueColorParser;

final readonly class RgbValueColorParser implements ValueColorParser
{
    /**
     * {@inheritDoc}
     */
    public static function getRegex(): string
    {
        return '/^rgb\((0|255|25[0-4]|2[0-4]\d|1\d\d|0?\d?\d),(0|255|25[0-4]|2[0-4]\d|1\d\d|0?\d?\d),(0|255|25[0-4]|2[0-4]\d|1\d\d|0?\d?\d)\)$/';
    }

    /**
     * {@inheritDoc}
     */
    public function parse(string $valueColor): string
    {
        $parsedValueColor = str_replace([' ', 'rgb(', ')'], '', $valueColor);

        $parsedValueColor = 'rgb('.$parsedValueColor.')';

        // Validate value color
        $results = [];
        preg_match(self::getRegex(), $parsedValueColor, $results);

        if (count($results) === 0) {
            throw new InvalidArgumentException('Unknown or invalid value color: '.$parsedValueColor);
        }

        return $parsedValueColor;
    }
}
