<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Colors;

use Tomloprod\Colority\Concerns\ResolvesContrastRatioColor;
use Tomloprod\Colority\Contracts\TransformableColor;
use Tomloprod\Colority\Contracts\ValueColorParser;

abstract class Color implements TransformableColor
{
    use ResolvesContrastRatioColor;

    protected string $valueColor;

    abstract public static function getParser(): ValueColorParser;

    final public function getValueColor(): string
    {
        return $this->valueColor;
    }
}
