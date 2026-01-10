<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Contracts;

use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Colors\OklchColor;
use Tomloprod\Colority\Colors\RgbColor;

interface TransformableColor
{
    public function toHex(): HexColor;

    public function toRgb(): RgbColor;

    public function toHsl(): HslColor;

    public function toOklch(): OklchColor;
}
