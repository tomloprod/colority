<?php

declare(strict_types=1);

use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Colors\RgbColor;

test('adjustLightness() increases lightness', function (): void {
    $color = new HslColor('hsl(200, 50, 50)');

    $lighter = $color->adjustLightness(20);

    expect($lighter)->toBeInstanceOf(HslColor::class);
    expect($lighter->getArrayValueColor()[2])->toBe(70.0);
});

test('adjustLightness() decreases lightness', function (): void {
    $color = new HslColor('hsl(200, 50, 50)');

    $darker = $color->adjustLightness(-20);

    expect($darker->getArrayValueColor()[2])->toBe(30.0);
});

test('adjustLightness() clamps to 0', function (): void {
    $color = new HslColor('hsl(200, 50, 10)');

    $darker = $color->adjustLightness(-50);

    expect($darker->getArrayValueColor()[2])->toBe(0.0);
});

test('adjustLightness() clamps to 100', function (): void {
    $color = new HslColor('hsl(200, 50, 90)');

    $lighter = $color->adjustLightness(50);

    expect($lighter->getArrayValueColor()[2])->toBe(100.0);
});

test('adjustSaturation() increases saturation', function (): void {
    $color = new HslColor('hsl(200, 50, 50)');

    $saturated = $color->adjustSaturation(20);

    expect($saturated->getArrayValueColor()[1])->toBe(70.0);
});

test('adjustSaturation() decreases saturation', function (): void {
    $color = new HslColor('hsl(200, 50, 50)');

    $desaturated = $color->adjustSaturation(-20);

    expect($desaturated->getArrayValueColor()[1])->toBe(30.0);
});

test('adjustSaturation() clamps to 0', function (): void {
    $color = new HslColor('hsl(200, 10, 50)');

    $desaturated = $color->adjustSaturation(-50);

    expect($desaturated->getArrayValueColor()[1])->toBe(0.0);
});

test('adjustSaturation() clamps to 100', function (): void {
    $color = new HslColor('hsl(200, 90, 50)');

    $saturated = $color->adjustSaturation(50);

    expect($saturated->getArrayValueColor()[1])->toBe(100.0);
});

test('lighter() increases lightness by default amount', function (): void {
    $color = new HslColor('hsl(200, 50, 50)');

    $lighter = $color->lighter();

    expect($lighter->getArrayValueColor()[2])->toBe(60.0);
});

test('darker() decreases lightness by default amount', function (): void {
    $color = new HslColor('hsl(200, 50, 50)');

    $darker = $color->darker();

    expect($darker->getArrayValueColor()[2])->toBe(40.0);
});

test('saturate() increases saturation by default amount', function (): void {
    $color = new HslColor('hsl(200, 50, 50)');

    $saturated = $color->saturate();

    expect($saturated->getArrayValueColor()[1])->toBe(60.0);
});

test('desaturate() decreases saturation by default amount', function (): void {
    $color = new HslColor('hsl(200, 50, 50)');

    $desaturated = $color->desaturate();

    expect($desaturated->getArrayValueColor()[1])->toBe(40.0);
});

test('methods return HslColor when called on any color type', function (): void {
    $hexColor = new HexColor('#808080');
    $rgbColor = new RgbColor('rgb(128, 128, 128)');
    $hslColor = new HslColor('hsl(200, 50, 50)');

    expect($hexColor->lighter())->toBeInstanceOf(HslColor::class);
    expect($rgbColor->darker())->toBeInstanceOf(HslColor::class);
    expect($hslColor->saturate())->toBeInstanceOf(HslColor::class);
});

test('methods preserve hue', function (): void {
    $color = new HslColor('hsl(200, 50, 50)');

    expect($color->lighter()->getArrayValueColor()[0])->toBe(200.0);
    expect($color->darker()->getArrayValueColor()[0])->toBe(200.0);
    expect($color->saturate()->getArrayValueColor()[0])->toBe(200.0);
    expect($color->desaturate()->getArrayValueColor()[0])->toBe(200.0);
});
