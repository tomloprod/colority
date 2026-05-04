<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Contracts;

use InvalidArgumentException;
use Tomloprod\Colority\Support\Dtos\ColorComponent;

interface ValueColorParser
{
    /**
     * @return string regex that validates the canonical string form of the color.
     */
    public static function getRegex(): string;

    /**
     * Parse a color value string into its components. Each component's value
     * is in the canonical numeric scale of the format, with the original unit
     * suffix preserved on the ColorComponent ('', '%', 'deg', 'rad', etc.).
     *
     * @return array<ColorComponent>
     *
     * @throws InvalidArgumentException when the input is not a valid color value.
     */
    public function parse(string $valueColor): array;
}
