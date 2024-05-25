<?php

declare(strict_types=1);

use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Colors\RgbColor;

test('toHex()', function (string $hslValueColor, string $hexValueColor): void {
    $hslColor = new HslColor($hslValueColor);

    $hexColor = $hslColor->toHex();

    expect($hexColor)->toBeInstanceOf(HexColor::class);

    expect($hexColor->getValueColor())->toBe($hexValueColor);

})->with([
    ['hsl(0deg,0,0)', '#000000'],
    ['hsl(0,100,50)', '#FF0000'],
    ['hsl(167.5deg,52.17%,27.06%)', '#21695A'],
    ['hsl(282.46,57.02,44.71)', '#8D31B3'],
    ['hsl(32.4deg,60.48%,51.37%)', '#CE8938'],
]);

test('toRgb()', function (string $hslValueColor, string $rgbValueColor): void {
    $hslColor = new HslColor($hslValueColor);

    $rgbColor = $hslColor->toRgb();

    expect($rgbColor)->toBeInstanceOf(RgbColor::class);

    expect($rgbColor->getValueColor())->toBe($rgbValueColor);

})->with([
    ['hsl(0,0,0)', 'rgb(0,0,0)'],
    ['hsl(0deg,100%,50)', 'rgb(255,0,0)'],
    ['hsl(167.5,52.17,27.06)', 'rgb(33,105,90)'],
    ['hsl(282.46deg,57.02%,44.71)', 'rgb(141,49,179)'],
    ['hsl(32.4,60.48,51.37)', 'rgb(206,137,56)'],
]);

test('toHsl()', function (string $hslValueColor): void {
    $hslColor = new HslColor($hslValueColor);

    expect($hslColor)->toBe($hslColor->toHsl());

    expect($hslColor->getValueColorWithMeasureUnits())->toBe($hslValueColor);

})->with([
    'hsl(0deg,0%,0%)', 'hsl(0deg,100%,50%)', 'hsl(167.5deg,52.17%,27.06%)', 'hsl(282.46deg,57.02%,44.71%)', 'hsl(32.4deg,60.48%,51.37%)',
]);
