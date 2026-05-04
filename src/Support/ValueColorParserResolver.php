<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support;

use InvalidArgumentException;
use Tomloprod\Colority\Colors\Color;
use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Colors\OklchColor;
use Tomloprod\Colority\Colors\RgbColor;

final readonly class ValueColorParserResolver
{
    /**
     * @param  array<int, class-string<Color>>  $colorClasses
     */
    public function __construct(
        private array $colorClasses = [
            HexColor::class,
            HslColor::class,
            RgbColor::class,
            OklchColor::class,
        ]
    ) {}

    /**
     * Try each color class in order and return the first one whose parser
     * accepts the input. CSS gradients (linear-gradient, radial-gradient,
     * conic-gradient) are detected and resolved to their dominant stop
     * color. Returns null if no color class matches.
     */
    public function parse(string $valueColor): ?Color
    {
        if (GradientResolver::isGradient($valueColor)) {
            return (new GradientResolver(
                fn (string $singleColorString): ?Color => $this->parseSingleColor($singleColorString),
            ))->resolveDominant($valueColor);
        }

        return $this->parseSingleColor($valueColor);
    }

    private function parseSingleColor(string $valueColor): ?Color
    {
        foreach ($this->colorClasses as $colorClass) {
            try {
                $colorClass::getParser()->parse($valueColor);
            } catch (InvalidArgumentException) {
                continue;
            }

            return new $colorClass($valueColor);
        }

        return null;
    }
}
