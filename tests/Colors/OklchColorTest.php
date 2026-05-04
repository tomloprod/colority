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

})->with([
    'oklch(0.5 0.1 180)',
    'oklch(0.7 0.15 120)',
    'oklch(0.6 0.2 90)',
    'oklch(0.505 0.213 27.518)',
]);

test('percentage form for L is equivalent to numeric 0-1 form', function (
    string $percentageForm,
    string $numericForm
): void {
    expect((new OklchColor($percentageForm))->getValueColor())
        ->toBe((new OklchColor($numericForm))->getValueColor());
})->with([
    ['oklch(0% 0.1 180)', 'oklch(0 0.1 180)'],
    ['oklch(50% 0.1 180)', 'oklch(0.5 0.1 180)'],
    ['oklch(70% 0.15 120)', 'oklch(0.7 0.15 120)'],
    ['oklch(100% 0.1 180)', 'oklch(1 0.1 180)'],
]);

test('percentage form for C is equivalent to numeric 0-0.4 form', function (
    string $percentageForm,
    string $numericForm
): void {
    expect((new OklchColor($percentageForm))->getValueColor())
        ->toBe((new OklchColor($numericForm))->getValueColor());
})->with([
    ['oklch(0.5 0% 180)', 'oklch(0.5 0 180)'],
    ['oklch(0.5 25% 180)', 'oklch(0.5 0.1 180)'],
    ['oklch(0.5 50% 180)', 'oklch(0.5 0.2 180)'],
    ['oklch(0.5 100% 180)', 'oklch(0.5 0.4 180)'],
]);

test('H angle units are converted to degrees', function (
    string $angleForm,
    string $degreesForm
): void {
    expect((new OklchColor($angleForm))->getValueColor())
        ->toBe((new OklchColor($degreesForm))->getValueColor());
})->with([
    ['oklch(0.5 0.1 180deg)', 'oklch(0.5 0.1 180)'],
    ['oklch(0.5 0.1 0.5turn)', 'oklch(0.5 0.1 180)'],
    ['oklch(0.5 0.1 1turn)', 'oklch(0.5 0.1 360)'],
    ['oklch(0.5 0.1 200grad)', 'oklch(0.5 0.1 180)'],
]);

test('formats very small float components without scientific notation', function (): void {
    $oklchColor = new OklchColor([1.0, 0.0000087, 263.28]);

    expect($oklchColor->getValueColor())->not->toContain('E');
    expect($oklchColor->getValueColor())->not->toContain('e');
});

test('getValueColorWithMeasureUnits returns the CSS-ready form', function (
    string $oklchValue,
    string $valueColorWithMeasureUnits
): void {
    expect((new OklchColor($oklchValue))->getValueColorWithMeasureUnits())
        ->toBe($valueColorWithMeasureUnits);
})->with([
    ['oklch(0.5 0.1 180)', 'oklch(50% 25% 180deg)'],
    ['oklch(0.7 0.15 120)', 'oklch(70% 37.5% 120deg)'],
    ['oklch(1 0.4 360)', 'oklch(100% 100% 360deg)'],
    ['oklch(0 0 0)', 'oklch(0% 0% 0deg)'],
]);
