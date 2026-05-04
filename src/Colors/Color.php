<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Colors;

use InvalidArgumentException;
use Tomloprod\Colority\Concerns\AdjustsColorValues;
use Tomloprod\Colority\Concerns\ResolvesContrastRatioColor;
use Tomloprod\Colority\Contracts\TransformableColor;
use Tomloprod\Colority\Contracts\ValueColorParser;
use Tomloprod\Colority\Support\Algorithms\LuminosityContrastRatio;
use Tomloprod\Colority\Support\Dtos\ColorComponent;

abstract class Color implements TransformableColor
{
    use AdjustsColorValues;
    use ResolvesContrastRatioColor;

    protected string $valueColor;

    /**
     * @var array<ColorComponent>
     */
    protected array $components;

    /**
     * @param  string|array<int|float>  $valueColor
     *
     * @throws InvalidArgumentException
     */
    final public function __construct(string|array $valueColor)
    {
        if (is_array($valueColor)) {
            $valueColor = $this->arrayToString($valueColor);
        }

        $this->components = static::getParser()->parse($valueColor);

        $this->valueColor = $this->buildCanonical($this->components);
    }

    abstract public static function getParser(): ValueColorParser;

    /**
     * Synthesize the canonical string representation of the color from its
     * parsed components. Each subclass owns its canonical format
     * (e.g. "#XXXXXX" for hex, "rgb(R,G,B)" for RGB, "oklch(L C H)" for OKLCH,
     * etc).
     *
     * @param  array<ColorComponent>  $components
     */
    abstract protected function buildCanonical(array $components): string;

    /**
     * Convert an array of component values into the format-specific string
     * that the parser knows how to consume (e.g. "rgb(R,G,B)" for RGB,
     * "#XXXXXX" for hex, "oklch(L C H)" for OKLCH).
     *
     * @param  array<int|float>  $values
     */
    abstract protected function arrayToString(array $values): string;

    final public function getValueColor(): string
    {
        return $this->valueColor;
    }

    final public function isEqualTo(self $color): bool
    {
        if (static::class === $color::class) {
            return $this->getValueColor() === $color->getValueColor();
        }

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
