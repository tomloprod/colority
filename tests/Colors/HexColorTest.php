<?php

declare(strict_types=1);

use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Colors\RgbColor;

test('toRgb()', function (string $hexValueColor, string $rgbValueColor): void {
    $hexColor = new HexColor($hexValueColor);

    $rgbColor = $hexColor->toRgb();

    expect($rgbColor)->toBeInstanceOf(RgbColor::class);

    expect($rgbColor->getValueColor())->toBe($rgbValueColor);
})->with([
    ['#000000', 'rgb(0,0,0)'],
    ['#FF0000', 'rgb(255,0,0)'],
    ['#21695A', 'rgb(33,105,90)'],
    ['#8D31B3', 'rgb(141,49,179)'],
    ['#CE8938', 'rgb(206,137,56)'],
]);

test('toHsl()', function (string $hexValueColor, string $hslValueColor): void {
    $hexColor = new HexColor($hexValueColor);

    $hslColor = $hexColor->toHsl();

    expect($hslColor)->toBeInstanceOf(HslColor::class);

    expect($hslColor->getValueColorWithMeasureUnits())->toBe($hslValueColor);
})->with([
    ['#000000', 'hsl(0deg,0%,0%)'],
    ['#FF0000', 'hsl(0deg,100%,50%)'],
    ['#21695A', 'hsl(167.5deg,52.17%,27.06%)'],
    ['#8D31B3', 'hsl(282.46deg,57.02%,44.71%)'],
    ['#CE8938', 'hsl(32.4deg,60.48%,51.37%)'],
]);

test('toHex()', function (string $hexValueColor): void {
    $hexColor = new HexColor($hexValueColor);

    expect($hexColor)->toBe($hexColor->toHex());

    expect($hexColor->getValueColor())->toBe($hexValueColor);

})->with(['#000000', '#CCCCCC', '#FFEEFF']);
