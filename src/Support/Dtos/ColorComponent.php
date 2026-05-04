<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Dtos;

/**
 * Represents a single component of a color value (e.g. the L of an OKLCH color)
 * after being normalized to its canonical numeric scale, while preserving the
 * unit suffix originally written by the user.
 *
 * The $value field is always in the canonical numeric form for that component
 * (e.g. OKLCH L is always 0-1, even if the user wrote "50%").
 *
 * The $unit field captures what the user originally wrote ('', '%', 'deg',
 * 'rad', 'turn', 'grad') so the original style can be reconstructed if needed.
 */
final readonly class ColorComponent
{
    public function __construct(
        public float $value,
        public string $unit = '',
    ) {}
}
