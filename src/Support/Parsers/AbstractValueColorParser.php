<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Parsers;

use InvalidArgumentException;
use Tomloprod\Colority\Contracts\ValueColorParser;
use Tomloprod\Colority\Support\Dtos\ColorComponent;

/**
 * Base class for value-color parsers. Provides the unit-aware component parser
 * shared across all formats. Concrete parsers declare their per-component unit
 * scale maps (e.g. ['%' => 0.01] for OKLCH lightness) and delegate the per-piece
 * conversion to {@see self::parseComponent()}.
 */
abstract class AbstractValueColorParser implements ValueColorParser
{
    /**
     * Parse a single component string by detecting an optional unit suffix and
     * scaling the numeric value to the component's canonical numeric form.
     *
     * The first matching suffix in $unitScales wins. If none matches the
     * component is treated as a bare number and returned as-is with an empty
     * unit.
     *
     * @param  array<string, float>  $unitScales  unit suffix => factor applied
     *                                            to the stripped numeric value.
     */
    final protected function parseComponent(string $raw, array $unitScales = []): ColorComponent
    {
        foreach ($unitScales as $unit => $factor) {
            if ($unit !== '' && str_ends_with($raw, $unit)) {
                $numeric = substr($raw, 0, -strlen($unit));

                if (! is_numeric($numeric)) {
                    throw new InvalidArgumentException('Unknown or invalid value color: '.$raw);
                }

                return new ColorComponent((float) $numeric * $factor, $unit);
            }
        }

        if (! is_numeric($raw)) {
            throw new InvalidArgumentException('Unknown or invalid value color: '.$raw);
        }

        return new ColorComponent((float) $raw);
    }

    /**
     * Split a component-only string (wrapper already stripped) into its raw
     * components. Accepts commas, whitespace or any combination thereof, as
     * permitted by CSS Color Module Level 4.
     *
     * @return array<int, string>
     */
    final protected function splitComponents(string $valueColor): array
    {
        $parts = preg_split('/[\s,]+/', trim($valueColor), -1, PREG_SPLIT_NO_EMPTY);

        return $parts === false ? [] : $parts;
    }
}
