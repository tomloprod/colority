<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support;

use InvalidArgumentException;
use Tomloprod\Colority\Colors\Color;
use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Colors\RgbColor;
use Tomloprod\Colority\Contracts\ValueColorParser;

final readonly class ValueColorParserResolver
{
    /**
     * Creates a new parsable content instance.
     *
     * @param  array<int, class-string<Color>>  $colorClasses
     */
    public function __construct(private array $colorClasses = [
        HexColor::class,
        HslColor::class,
        RgbColor::class,
    ])
    {
    }

    /**
     * Parses the value color
     */
    public function parse(string $valueColor): ?Color
    {
        /** @var Color|null $color */
        $color = null;

        /** @var class-string<Color> $colorClass */
        foreach ($this->colorClasses as $colorClass) {
            try {
                /** @var ValueColorParser $parser */
                $parser = new ($colorClass::getParser());

                $color = new $colorClass($parser->parse($valueColor));

                break;
            } catch (InvalidArgumentException) {
            }
        }

        return $color;
    }
}
