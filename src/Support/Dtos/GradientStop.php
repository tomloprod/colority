<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Dtos;

use Tomloprod\Colority\Colors\Color;

/**
 * A single color-stop inside a CSS gradient: a parsed Color and its position
 * along the gradient axis (0-100). Position is nullable while we are reading
 * the user input (some stops can omit it) and gets filled in by the resolver
 * before the dominance calculation runs.
 */
final class GradientStop
{
    public function __construct(
        public Color $color,
        public ?float $position = null,
    ) {}
}
