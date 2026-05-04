<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Colors;

use Tomloprod\Colority\Contracts\ValueColorParser;
use Tomloprod\Colority\Support\Dtos\ColorComponent;
use Tomloprod\Colority\Support\Parsers\HexValueColorParser;

final class HexColor extends Color
{
    public static function getParser(): ValueColorParser
    {
        return new HexValueColorParser();
    }

    /**
     * @return array<int> RGB
     */
    public function getArrayValueColor(): array
    {
        return [
            (int) $this->components[0]->value,
            (int) $this->components[1]->value,
            (int) $this->components[2]->value,
        ];
    }

    public function toRgb(): RgbColor
    {
        [$r, $g, $b] = $this->getArrayValueColor();

        return new RgbColor([$r, $g, $b]);
    }

    public function toHsl(): HslColor
    {
        return $this->toRgb()->toHsl();
    }

    public function toHex(): self
    {
        return $this;
    }

    public function toOklch(): OklchColor
    {
        return $this->toRgb()->toOklch();
    }

    /**
     * @param  array<ColorComponent>  $components
     */
    protected function buildCanonical(array $components): string
    {
        return sprintf(
            '#%02X%02X%02X',
            (int) $components[0]->value,
            (int) $components[1]->value,
            (int) $components[2]->value,
        );
    }

    /**
     * @param  array<int|float>  $values  RGB byte values [R, G, B].
     */
    protected function arrayToString(array $values): string
    {
        return sprintf(
            '#%02X%02X%02X',
            (int) ($values[0] ?? 0),
            (int) ($values[1] ?? 0),
            (int) ($values[2] ?? 0),
        );
    }
}
