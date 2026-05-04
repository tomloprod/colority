<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Contracts;

/**
 * Implemented by colors whose canonical components carry measure units (e.g. HSL
 * uses degrees and percentages, OKLCH uses percentages and degrees). Provides a
 * single CSS-ready string that includes the conventional unit suffixes for the
 * format.
 */
interface HasMeasureUnits
{
    public function getValueColorWithMeasureUnits(): string;
}
