<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Concerns;

use Tomloprod\Colority\Colors\HslColor;

trait AdjustsColorValues
{
    /**
     * Adjust the lightness by a percentage.
     *
     * @param  float  $percent  Positive to brighten, negative to darken.
     */
    public function adjustLightness(float $percent): HslColor
    {
        [$h, $s, $l] = $this->toHsl()->getArrayValueColor();

        $l = max(0, min(100, $l + $percent));

        return new HslColor([$h, $s, $l]);
    }

    /**
     * Adjust the saturation by a percentage.
     *
     * @param  float  $percent  Positive to saturate, negative to desaturate.
     */
    public function adjustSaturation(float $percent): HslColor
    {
        [$h, $s, $l] = $this->toHsl()->getArrayValueColor();

        $s = max(0, min(100, $s + $percent));

        return new HslColor([$h, $s, $l]);
    }

    /**
     * Increase the lightness (make brighter).
     */
    public function lighter(float $amount = 10): HslColor
    {
        return $this->adjustLightness($amount);
    }

    /**
     * Decrease the lightness (make darker).
     */
    public function darker(float $amount = 10): HslColor
    {
        return $this->adjustLightness(-$amount);
    }

    /**
     * Increase the saturation.
     */
    public function saturate(float $amount = 10): HslColor
    {
        return $this->adjustSaturation($amount);
    }

    /**
     * Decrease the saturation.
     */
    public function desaturate(float $amount = 10): HslColor
    {
        return $this->adjustSaturation(-$amount);
    }
}
