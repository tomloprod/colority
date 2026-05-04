<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support;

/**
 * Helpers for formatting floating-point values into the canonical string form
 * used by the library.
 */
final class FloatFormatter
{
    /**
     * Format a float as a fixed-point decimal with up to 6 decimal places,
     * trimming trailing zeros and the trailing decimal point. Avoids the
     * scientific notation that PHP's default float-to-string conversion
     * produces for very small values (e.g. 8.7E-5).
     */
    public static function asFixedPoint(float $value): string
    {
        return rtrim(rtrim(sprintf('%.6F', $value), '0'), '.');
    }
}
