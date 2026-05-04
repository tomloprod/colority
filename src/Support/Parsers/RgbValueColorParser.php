<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Parsers;

use InvalidArgumentException;
use Tomloprod\Colority\Support\FloatFormatter;

final class RgbValueColorParser extends AbstractValueColorParser
{
    public static function getRegex(): string
    {
        return '/^rgb\((0|255|25[0-4]|2[0-4]\d|1\d\d|0?\d?\d),(0|255|25[0-4]|2[0-4]\d|1\d\d|0?\d?\d),(0|255|25[0-4]|2[0-4]\d|1\d\d|0?\d?\d)\)$/';
    }

    public function parse(string $valueColor): array
    {
        $stripped = str_replace(['rgb(', ')'], '', $valueColor);
        $parts = $this->splitComponents($stripped);

        if (count($parts) !== 3) {
            throw new InvalidArgumentException('Unknown or invalid value color: '.$valueColor);
        }

        $components = [
            $this->parseComponent($parts[0]),
            $this->parseComponent($parts[1]),
            $this->parseComponent($parts[2]),
        ];

        $canonical = sprintf(
            'rgb(%s,%s,%s)',
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
