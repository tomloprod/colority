<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Concerns;

use Tomloprod\Colority\Colors\Color;
use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Support\Algorithms\ContrastRatioScore;
use Tomloprod\Colority\Support\Algorithms\LuminosityContrastRatio;

trait ResolvesContrastRatioColor
{
    /**
     * Find the best foreground color from a list of candidates or default to black/white.

     *
     * @param  array<Color>  $foregroundColors  - Candidates for the best foreground color.
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

    /**
     * Find a foreground color that matches this color's hue while meeting WCAG contrast requirements:
     * generates a new color by adjusting lightness while preserving the original hue.
     *
     * @param  ContrastRatioScore  $targetScore  The minimum WCAG contrast level to achieve.
     * @param  int  $lightnessStep  The amount of lightness to jump in each iteration.
     */
    public function getMatchingForegroundColor(
        ContrastRatioScore $targetScore = ContrastRatioScore::Good,
        int $lightnessStep = 1
    ): Color {
        [$h, $s, $l] = $this->toHsl()->getArrayValueColor();

        $targetContrast = $targetScore->getMinimumScore();

        $lightnessStep = max(1, min(100, $lightnessStep));

        // Determine step: if lightness > 50%, go darker; otherwise go lighter
        $step = ($l > 50) ? -$lightnessStep : $lightnessStep;

        for ($testL = $l + $step; $testL >= 0 && $testL <= 100; $testL += $step) {
            $testColor = new HslColor([$h, $s, $testL]);

            if ($this->getContrastRatio($testColor) >= $targetContrast) {
                return $testColor;
            }
        }

        // Return the best foreground color as fallback
        return $this->getBestForegroundColor();
    }
}
