<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Algorithms;

/**
 * @see https://www.w3.org/TR/WCAG22/#dfn-relative-luminance
 * @see https://www.w3.org/TR/WCAG20-TECHS/G17.html
 */
final class LuminosityContrastRatio
{
    /**
     * Calculate the luminosity contrast ratio between two RGB colors (background
     * and foreground).
     *
     * - greater or equal than 7: Excellent contrast ratio (AAA level for normal text, AA level for large text)
     * - greater or equal than 4.5: Good contrast ratio (AA level for normal text, AAA level for large text)
     * - greater or equal than 3: Acceptable contrast ratio (AA level for large text)
     * - less than 3: Insufficient contrast ratio
     *
     * @param  array<int>  $rgbBackgroundColor  RGB values of the background color.
     * @param  array<int>  $rgbForegroundColor  RGB values of the foreground color.
     * @return float Luminosity contrast ratio.
     */
    public function getContrastRatio(array $rgbBackgroundColor, array $rgbForegroundColor): float
    {
        $lum1 = $this->getLuminance($rgbBackgroundColor[0], $rgbBackgroundColor[1], $rgbBackgroundColor[2]);
        $lum2 = $this->getLuminance($rgbForegroundColor[0], $rgbForegroundColor[1], $rgbForegroundColor[2]);

        // @codeCoverageIgnoreStart
        // @codeCoverageIgnoreEnd

        // Ensure L1 is the lighter luminance and L2 is the darker luminance
        $L1 = max($lum1, $lum2);
        $L2 = min($lum1, $lum2);

        // Calculate the contrast ratio
        $contrastRatio = ($L1 + 0.05) / ($L2 + 0.05);

        // Truncate to 2 decimals without rounding
        return floor($contrastRatio * 100) / 100;
    }

    /**
     * Calculate the luminosity of an RGB color.
     *
     * @param  int  $r  Red value (0-255).
     * @param  int  $g  Green value (0-255).
     * @param  int  $b  Blue value (0-255).
     * @return float Luminosity.
     */
    private function getLuminance(int $r, int $g, int $b): float
    {
        // Convert RGB from 8-bit to sRGB
        $srgbRed = $r / 255;
        $srgbGreen = $g / 255;
        $srgbBlue = $b / 255;

        // Apply the formula to convert sRGB to linear RGB
        $rLinear = ($srgbRed <= 0.03928) ? $srgbRed / 12.92 : (($srgbRed + 0.055) / 1.055) ** 2.4;
        $gLinear = ($srgbGreen <= 0.03928) ? $srgbGreen / 12.92 : (($srgbGreen + 0.055) / 1.055) ** 2.4;
        $bLinear = ($srgbBlue <= 0.03928) ? $srgbBlue / 12.92 : (($srgbBlue + 0.055) / 1.055) ** 2.4;

        // Calculate the relative luminance
        $luminance = 0.2126 * $rLinear + 0.7152 * $gLinear + 0.0722 * $bLinear;

        return $luminance;
    }
}
