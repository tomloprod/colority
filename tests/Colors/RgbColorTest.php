<?php

declare(strict_types=1);

use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\RgbColor;

test('toHex()', function (string $rgbValueColor, string $hexValueColor): void {
    $rgbColor = new RgbColor($rgbValueColor);

    $hexColor = $rgbColor->toHex();

    expect($hexColor)->toBeInstanceOf(HexColor::class);

    expect($hexColor->getValueColor())->toBe($hexValueColor);
})->with([
    ['rgb(0,0,0)', '#000000'],
]);

test('toRgb()', function (string $rgbValueColor): void {
    $rgbColor = new RgbColor($rgbValueColor);

    expect($rgbColor)->toBe($rgbColor->toRgb());

    expect($rgbColor->getValueColor())->toBe($rgbValueColor);

})->with(['rgb(255,255,255)', 'rgb(123,123,123)', 'rgb(0,0,0)']);
