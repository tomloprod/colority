<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Dtos;

use Tomloprod\Colority\Colors\RgbColor;

/**
 * Represents a color found in an image with its frequency metadata.
 */
final readonly class ImageColorFrequency
{
    public function __construct(
        public RgbColor $color,
        public float $percentage,
        public int $pixelCount,
    ) {}
}
