<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Parsers;

use InvalidArgumentException;
use Tomloprod\Colority\Support\FloatFormatter;

final class OklchValueColorParser extends AbstractValueColorParser
{
    public static function getRegex(): string
    {
        return '/^oklch\(\s*(0|1|0?\.\d+)\s+(0|[01](?:\.\d+)?)\s+((?:360|(?:0|[1-9]\d?|[12]\d\d|3[0-5]\d)(?:\.\d+)?))\s*\)$/';
    }

    public function parse(string $valueColor): array
    {
        $stripped = str_replace(['oklch(', ')'], '', $valueColor);
        $parts = $this->splitComponents($stripped);

        if (count($parts) !== 3) {
            throw new InvalidArgumentException('Unknown or invalid value color: '.$valueColor);
        }

        // L: number 0-1 or percentage 0%-100% (100% = 1).
        // C: number 0-0.4 or percentage 0%-100% (100% = 0.4).
        // H: number (degrees) or angle units (deg/rad/turn/grad).
        $components = [
            $this->parseComponent($parts[0], ['%' => 0.01]),
            $this->parseComponent($parts[1], ['%' => 0.004]),
            // Unit suffixes ordered by length (longest first) so that 'grad'
            // is detected before 'rad' on inputs like '0.5grad'.
            $this->parseComponent($parts[2], [
                'turn' => 360.0,
                'grad' => 0.9,
                'rad' => 180.0 / M_PI,
                'deg' => 1.0,
            ]),
        ];

        $canonical = sprintf(
            'oklch(%s %s %s)',
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
