<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Colors;

use Tomloprod\Colority\Concerns\AdjustsColorValues;
use Tomloprod\Colority\Concerns\ResolvesContrastRatioColor;
use Tomloprod\Colority\Contracts\TransformableColor;
use Tomloprod\Colority\Contracts\ValueColorParser;
use Tomloprod\Colority\Support\Algorithms\LuminosityContrastRatio;

abstract class Color implements TransformableColor
{
    use AdjustsColorValues;
    use ResolvesContrastRatioColor;

    protected string $valueColor;

    abstract public static function getParser(): ValueColorParser;

    final public function getValueColor(): string
    {
        return $this->valueColor;
    }

    final public function isEqualTo(self $color): bool
    {
        return $this->toHex()->getValueColor() === $color->toHex()->getValueColor();
    }

    /**
     * Get the relative luminance of the color (WCAG 2.0).
     *
     * @see https://www.w3.org/TR/WCAG22/#dfn-relative-luminance
     */
    final public function getLuminance(): float
    {
        [$r, $g, $b] = $this->toRgb()->getArrayValueColor();

        return (new LuminosityContrastRatio())->getLuminance($r, $g, $b);
    }

    /**
     * Determine if the color is considered dark.
     */
    final public function isDark(): bool
    {
        return $this->getLuminance() < LuminosityContrastRatio::DARK_LUMINANCE_THRESHOLD;
    }

    /**
     * Determine if the color is considered light.
     */
    final public function isLight(): bool
    {
        return ! $this->isDark();
    }

    /**
     * Determine if this color is darker than the given color.
     */
    final public function isDarkerThan(self $color): bool
    {
        return $this->getLuminance() < $color->getLuminance();
    }

    /**
     * Determine if this color is lighter than the given color.
     */
    final public function isLighterThan(self $color): bool
    {
        return $this->getLuminance() > $color->getLuminance();
    }
}
