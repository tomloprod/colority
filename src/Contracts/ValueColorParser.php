<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Contracts;

use InvalidArgumentException;

interface ValueColorParser
{
    /**
     * @return string regex to validate value color string.
     */
    public static function getRegex(): string;

    /**
     * @throws InvalidArgumentException when receive ar unknown or invalid value color
     */
    public function parse(string $valueColor): string;
}
