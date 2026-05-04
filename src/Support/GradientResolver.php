<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support;

use Closure;
use Tomloprod\Colority\Colors\Color;
use Tomloprod\Colority\Support\Dtos\GradientStop;

/**
 * Detects CSS gradient functions (linear-gradient, radial-gradient,
 * conic-gradient) and returns the dominant single color of the gradient,
 * computed as the stop whose visual area on the gradient axis is the
 * largest.
 *
 * Modifiers like "!important" are stripped. Metadata segments (angles,
 * shapes, "from N at X Y" prefixes) are skipped because they do not parse
 * as colors.
 *
 * Single-color parsing is delegated to a closure provided by the caller,
 * which avoids a circular dependency on ValueColorParserResolver.
 */
final readonly class GradientResolver
{
    private const GRADIENT_PATTERN = '/^(?:linear|radial|conic)-gradient\s*\((.*)\)\s*$/is';

    /**
     * @param  Closure(string): ?Color  $singleColorParser
     */
    public function __construct(private Closure $singleColorParser) {}

    public static function isGradient(string $valueColor): bool
    {
        return preg_match(self::GRADIENT_PATTERN, self::cleanModifiers($valueColor)) === 1;
    }

    public function resolveDominant(string $valueColor): ?Color
    {
        $cleaned = self::cleanModifiers($valueColor);

        if (preg_match(self::GRADIENT_PATTERN, $cleaned, $matches) !== 1) {
            return null;
        }

        $stops = $this->extractStops($matches[1]);

        if ($stops === []) {
            return null;
        }

        if (count($stops) === 1) {
            return $stops[0]->color;
        }

        $this->fillMissingPositions($stops);

        return $this->pickDominant($stops);
    }

    private static function cleanModifiers(string $valueColor): string
    {
        $cleaned = preg_replace('/!\s*important/i', '', $valueColor);

        return trim((string) $cleaned);
    }

    /**
     * @return array<int, GradientStop>
     */
    private function extractStops(string $inner): array
    {
        $stops = [];

        foreach ($this->splitTopLevelCommas($inner) as $segment) {
            $stop = $this->parseStop($segment);

            if ($stop instanceof GradientStop) {
                $stops[] = $stop;
            }
        }

        return $stops;
    }

    /**
     * @return array<int, string>
     */
    private function splitTopLevelCommas(string $inner): array
    {
        $segments = [];
        $current = '';
        $depth = 0;
        $length = strlen($inner);

        for ($i = 0; $i < $length; $i++) {
            $char = $inner[$i];

            if ($char === '(') {
                $depth++;
                $current .= $char;
            } elseif ($char === ')') {
                $depth--;
                $current .= $char;
            } elseif ($char === ',' && $depth === 0) {
                $segments[] = $current;
                $current = '';
            } else {
                $current .= $char;
            }
        }

        if ($current !== '') {
            $segments[] = $current;
        }

        return $segments;
    }

    private function parseStop(string $segment): ?GradientStop
    {
        $trimmed = trim($segment);
        $singleColorParser = $this->singleColorParser;

        // Try "<color> <position>%"
        if (preg_match('/^(.+?)\s+([\d.]+)%\s*$/', $trimmed, $matches) === 1) {
            $color = $singleColorParser($matches[1]);

            if ($color !== null && is_numeric($matches[2])) {
                $position = max(0.0, min(100.0, (float) $matches[2]));

                return new GradientStop($color, $position);
            }
        }

        // Try as color only (no position)
        $color = $singleColorParser($trimmed);

        if ($color === null) {
            return null;
        }

        return new GradientStop($color);
    }

    /**
     * Fill in the positions that were not declared by the user, following
     * the CSS rules: missing first stop defaults to 0, missing last stop
     * defaults to 100, and intermediate gaps are evenly distributed
     * between the surrounding declared positions.
     *
     * @param  array<int, GradientStop>  $stops
     */
    private function fillMissingPositions(array $stops): void
    {
        $count = count($stops);

        if ($stops[0]->position === null) {
            $stops[0]->position = 0.0;
        }

        if ($stops[$count - 1]->position === null) {
            $stops[$count - 1]->position = 100.0;
        }

        $i = 1;
        while ($i < $count - 1) {
            if ($stops[$i]->position !== null) {
                $i++;

                continue;
            }

            $gapEnd = $i;
            while ($gapEnd < $count - 1 && $stops[$gapEnd]->position === null) {
                $gapEnd++;
            }

            $startPosition = (float) $stops[$i - 1]->position;
            $endPosition = (float) $stops[$gapEnd]->position;
            $segmentCount = $gapEnd - $i + 1;

            for ($k = $i; $k < $gapEnd; $k++) {
                $stops[$k]->position = $startPosition + ($endPosition - $startPosition) * (($k - $i + 1) / $segmentCount);
            }

            $i = $gapEnd;
        }
    }

    /**
     * Pick the stop whose dominance interval (midpoints with neighbors,
     * clamped to [0,100] at the ends) is the widest. Ties resolve to the
     * first stop in declaration order.
     *
     * @param  array<int, GradientStop>  $stops
     */
    private function pickDominant(array $stops): Color
    {
        $count = count($stops);
        $maxWidth = -1.0;
        $dominant = $stops[0]->color;

        foreach ($stops as $i => $stop) {
            $previousMid = $i === 0
                ? 0.0
                : ((float) $stops[$i - 1]->position + (float) $stop->position) / 2;

            $nextMid = $i === $count - 1
                ? 100.0
                : ((float) $stop->position + (float) $stops[$i + 1]->position) / 2;

            $width = $nextMid - $previousMid;

            if ($width > $maxWidth) {
                $maxWidth = $width;
                $dominant = $stop->color;
            }
        }

        return $dominant;
    }
}
