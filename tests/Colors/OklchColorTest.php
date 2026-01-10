<?php

declare(strict_types=1);

use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Colors\OklchColor;
use Tomloprod\Colority\Colors\RgbColor;

test('toRgb()', function (string $oklchValue, string $rgbValue): void {
    $oklchColor = new OklchColor($oklchValue);

    $rgbColor = $oklchColor->toRgb();

    expect($rgbColor)->toBeInstanceOf(RgbColor::class);

    expect($rgbColor->getValueColor())->toBe($rgbValue);
})->with([
    ['oklch(0.5 0.1 180)', 'rgb(0,117,101)'],
    ['oklch(0.7 0.15 120)', 'rgb(147,171,44)'],
    ['oklch(0.6 0.2 90)', 'rgb(174,117,0)'],
]);

test('toHex()', function (string $oklchValue, string $hexValue): void {
    $oklchColor = new OklchColor($oklchValue);

    $hexColor = $oklchColor->toHex();

    expect($hexColor)->toBeInstanceOf(HexColor::class);

    expect($hexColor->getValueColor())->toBe($hexValue);
})->with([
    ['oklch(0.5 0.1 180)', '#007565'],
    ['oklch(0.7 0.15 120)', '#93AB2C'],
    ['oklch(0.6 0.2 90)', '#AE7500'],
]);

test('toHsl()', function (string $oklchValue, string $hslValue): void {
    $oklchColor = new OklchColor($oklchValue);

    $hslColor = $oklchColor->toHsl();

    expect($hslColor)->toBeInstanceOf(HslColor::class);

    expect($hslColor->getValueColor())->toBe($hslValue);
})->with([
    ['oklch(0.5 0.1 180)', 'hsl(171.79,100,22.94)'],
    ['oklch(0.7 0.15 120)', 'hsl(71.34,59.07,42.16)'],
    ['oklch(0.6 0.2 90)', 'hsl(40.34,100,34.12)'],
]);

test('toOklch()', function (string $oklchValue): void {
    $oklchColor = new OklchColor($oklchValue);

    expect($oklchColor)->toBe($oklchColor->toOklch());

    expect($oklchColor->getValueColor())->toBe($oklchValue);

})->with(['oklch(0.5 0.1 180)', 'oklch(0.7 0.15 120)', 'oklch(0.6 0.2 90)']);
