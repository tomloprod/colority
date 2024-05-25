<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Concerns;

use Tomloprod\Colority\Colors\Color;
use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Support\Algorithms\LuminosityContrastRatio;

trait ResolvesContrastRatioColor
{
    /**
     * @param  array<Color>  $foregroundColors
     */
    public function getBestForegroundColor(array $foregroundColors = []): Color
    {
        if ($foregroundColors === []) {
            $foregroundColors[] = new HexColor('#000000');
            $foregroundColors[] = new HexColor('#FFFFFF');
        }

        /** @var float $bestContrastRatio */
        $bestContrastRatio = 0;

        /** @var Color $bestColor */
        $bestColor = $foregroundColors[0];

        /** @var Color $color */
        foreach ($foregroundColors as $color) {
            /** @var float $contrastRatio */
            $contrastRatio = $this->getContrastRatio($color->toRgb());

            if ($contrastRatio > $bestContrastRatio) {
                $bestContrastRatio = $contrastRatio;
                $bestColor = $color;
            }
        }

        return $bestColor;
    }

    /**
     * @param  Color|null  $foregroundColor  #000000 by default
     */
    public function getContrastRatio(?Color $foregroundColor = null): float
    {
        if (! $foregroundColor instanceof Color) {
            $foregroundColor = new HexColor('#000000');
        }

        $lumContrastRatio = new LuminosityContrastRatio();

        return $lumContrastRatio->getContrastRatio(
            $this->toRgb()->getArrayValueColor(),
            $foregroundColor->toRgb()->getArrayValueColor()
        );
    }
}
