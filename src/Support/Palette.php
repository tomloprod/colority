<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support;

use Tomloprod\Colority\Colors\Color;
use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;

/**
 * Immutable mapping of semantic names (primary, success, danger, ...) to
 * their Color values. Provides a default palette and immutable update
 * methods.
 */
final readonly class Palette
{
    /**
     * @param  array<string, Color>  $colors
     */
    public function __construct(private array $colors) {}

    public static function default(): self
    {
        return new self(self::defaultColors());
    }

    /**
     * Build a palette around a brand color: primary becomes the brand,
     * secondary is the complementary hue with reduced saturation, light and
     * dark are the brand brightened and darkened.
     */
    public static function buildFromColor(Color $brandColor): self
    {
        [$h, $s, $l] = $brandColor->toHsl()->getArrayValueColor();

        $secondary = new HslColor([
            fmod($h + 180.0, 360.0),
            max(10.0, $s * 0.4),
            $l,
        ]);

        $defaults = self::defaultColors();

        return new self([
            'primary' => $brandColor,
            'secondary' => $secondary,
            'success' => $defaults['success'],
            'danger' => $defaults['danger'],
            'warning' => $defaults['warning'],
            'info' => $defaults['info'],
            'light' => $brandColor->lighter(40),
            'dark' => $brandColor->darker(40),
        ]);
    }

    public function get(string $name): ?Color
    {
        return $this->colors[$name] ?? null;
    }

    public function with(string $name, Color $color): self
    {
        return new self([...$this->colors, $name => $color]);
    }

    /**
     * @param  array<string, Color>  $palette
     */
    public function withMany(array $palette): self
    {
        return new self([...$this->colors, ...$palette]);
    }

    /**
     * @return array<string, Color>
     */
    private static function defaultColors(): array
    {
        return [
            'primary' => new HexColor('#5B8DEF'),
            'secondary' => new HexColor('#A1A8B3'),
            'success' => new HexColor('#4BD396'),
            'danger' => new HexColor('#F5707A'),
            'warning' => new HexColor('#F9C851'),
            'info' => new HexColor('#3AC9D6'),
            'light' => new HexColor('#F4F6F8'),
            'dark' => new HexColor('#1F2937'),
        ];
    }
}
