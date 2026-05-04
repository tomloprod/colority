<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Parsers;

use InvalidArgumentException;
use Tomloprod\Colority\Support\Dtos\ColorComponent;

final class HexValueColorParser extends AbstractValueColorParser
{
    public static function getRegex(): string
    {
        return '/^#(?:[0-9a-fA-F]{3}){1,2}$/';
    }

    public function parse(string $valueColor): array
    {
        $hex = str_replace([' ', '#'], '', $valueColor);

        if (mb_strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $canonical = '#'.strtoupper($hex);

        if (preg_match(self::getRegex(), $canonical) !== 1) {
            throw new InvalidArgumentException('Unknown or invalid value color');
        }

        return [
            new ColorComponent((float) hexdec(substr($canonical, 1, 2))),
            new ColorComponent((float) hexdec(substr($canonical, 3, 2))),
            new ColorComponent((float) hexdec(substr($canonical, 5, 2))),
        ];
    }
}
