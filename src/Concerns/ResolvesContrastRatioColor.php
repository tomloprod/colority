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

        $bestContrastRatio = 0.0;

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
     * Returns a foreground color that preserves this color's hue and saturation
     * while attempting to meet the requested WCAG contrast ratio. If the target
     * cannot be reached without changing the hue, returns the closest
     * hue-preserving approximation; contrast in that case is best-effort.
     *
     * @param  ContrastRatioScore  $targetScore  The minimum WCAG contrast level to achieve.
     * @param  int  $lightnessStep  The amount of lightness to jump in each iteration.
     */
    public function getMatchingForegroundColor(
        ContrastRatioScore $targetScore = ContrastRatioScore::Excellent,
        int $lightnessStep = 1
    ): Color {
        [$h, $s, $l] = $this->toHsl()->getArrayValueColor();

        $targetContrast = $targetScore->getMinimumScore();

        $lightnessStep = max(1, min(100, $lightnessStep));

        // Determine step: if lightness > 50%, go darker; otherwise go lighter
        $primaryStep = ($l > 50) ? -$lightnessStep : $lightnessStep;

        foreach ([$primaryStep, -$primaryStep] as $step) {
            for ($testL = $l + $step; $testL >= 0 && $testL <= 100; $testL += $step) {
                $testColor = new HslColor([$h, $s, $testL]);

                if ($this->getContrastRatio($testColor) >= $targetContrast) {
                    return $testColor;
                }
            }
        }

        // Return the best foreground color as fallback
        return $this->getBestForegroundColor();
    }
}
