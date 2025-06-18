<?php

declare(strict_types=1);

use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Colors\RgbColor;
use Tomloprod\Colority\Services\ColorityManager;

it('throws exception on clone', function (): void {
    $instance = ColorityManager::instance();

    $closure = fn (): mixed => clone $instance;

    expect($closure)->toThrow(Exception::class, 'Cannot clone singleton');
});

it('throws exception on unserialize', function (): void {
    $instance = ColorityManager::instance();

    $closure = fn (): mixed => unserialize(serialize($instance));

    expect($closure)->toThrow(Exception::class, 'Cannot unserialize singleton');
});

it('returns the same instance', function (): void {
    $instance1 = ColorityManager::instance();
    $instance2 = ColorityManager::instance();

    expect($instance1)->toBe($instance2);
});

test('textToColor', function (string $text, string $hsl): void {
    $instance = ColorityManager::instance();

    expect($instance->textToColor($text)->getValueColorWithMeasureUnits())->toBe($hsl);
})->with([
    ['tomloprod', 'hsl(77deg,56%,13%)'],
    ['Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ', 'hsl(239deg,69%,27%)'],
    ['Colority', 'hsl(315deg,73%,64%)'],
]);

test('textToColor with fromColor and toColor interpolates between colors', function (): void {
    $instance = ColorityManager::instance();

    $fromColor = new HslColor('hsl(0deg,100%,50%)'); // Red
    $toColor = new HslColor('hsl(240deg,100%,50%)'); // Blue

    $result = $instance->textToColor('test', $fromColor, $toColor);

    expect($result)->toBeInstanceOf(HslColor::class);

    // The result should be deterministic for the same input
    $result2 = $instance->textToColor('test', $fromColor, $toColor);
    expect($result->getValueColorWithMeasureUnits())->toBe($result2->getValueColorWithMeasureUnits());

    // Different texts should produce different results
    $result3 = $instance->textToColor('different text', $fromColor, $toColor);
    expect($result->getValueColorWithMeasureUnits())->not()->toBe($result3->getValueColorWithMeasureUnits());
});

test('textToColor with fromColor generates variations around base color', function (): void {
    $instance = ColorityManager::instance();

    $baseColor = new HslColor('hsl(120deg,60%,40%)'); // Green base

    $result = $instance->textToColor('test', $baseColor);

    expect($result)->toBeInstanceOf(HslColor::class);

    // The result should be deterministic for the same input
    $result2 = $instance->textToColor('test', $baseColor);
    expect($result->getValueColorWithMeasureUnits())->toBe($result2->getValueColorWithMeasureUnits());

    // Different texts should produce different variations
    $result3 = $instance->textToColor('different text', $baseColor);
    expect($result->getValueColorWithMeasureUnits())->not()->toBe($result3->getValueColorWithMeasureUnits());

    // The result should be reasonably close to the base color
    [$resultH, $resultS, $resultL] = $result->getArrayValueColor();
    [$baseH, $baseS, $baseL] = $baseColor->getArrayValueColor();

    // Verify variations are within expected ranges (±30° hue, ±10% saturation/lightness)
    expect(abs($resultH - $baseH))->toBeLessThanOrEqual(30);
    expect(abs($resultS - $baseS))->toBeLessThanOrEqual(10);
    expect(abs($resultL - $baseL))->toBeLessThanOrEqual(10);
});

test('getSimilarColor', function (): void {
    $instance = ColorityManager::instance();

    expect($instance->getSimilarColor(new HslColor('hsl(77deg,56%,13%)')))->toBeInstanceOf(HslColor::class);
});

/**
 * Hex
 */
test('fromHex() with right value color returns HexColor instance', function (string $hexColor): void {
    $colority = ColorityManager::instance();

    $closure = fn (): mixed => $colority->fromHex($hexColor);

    expect($closure)->not()->toThrow(InvalidArgumentException::class, 'Unknown or invalid value color');

    expect($colority->fromHex($hexColor))->toBeInstanceOf(HexColor::class);

    expect($colority->parse($hexColor))->toBeInstanceOf(HexColor::class);

})->with(['#FFF', '000', '#FF0000', '00F', '#0000FF', 'FFF', '#00FFFF', 'FF0', '#808080', 'A52A2A']);

test('fromHex() with invalid value color throws InvalidArgumentException', function (string $hexColor): void {
    $colority = ColorityManager::instance();

    $closure = fn (): mixed => $colority->fromHex($hexColor);

    expect($closure)->toThrow(InvalidArgumentException::class, 'Unknown or invalid value color');

    expect($colority->parse($hexColor))->toBeNull();

})->with(['#CC', '#tomlop', '####', '#***']);

/**
 * RGB
 */
test('fromRgb() with right value color returns RgbColor instance', function (string $rgbColor): void {
    $colority = ColorityManager::instance();

    expect(fn (): mixed => $colority->fromRgb($rgbColor))->not()->toThrow(InvalidArgumentException::class, 'Unknown or invalid value color');

    expect($colority->fromRgb($rgbColor))->toBeInstanceOf(RgbColor::class);

    expect($colority->parse($rgbColor))->toBeInstanceOf(RgbColor::class);

})->with(['rgb(255,255,255)', '255,255,255', 'rgb(0,0,0)', '125, 125, 125']);

test('fromRgb() with array right value color returns RgbColor instance', function (string $rgbColor): void {
    $colority = ColorityManager::instance();

    $rgbColor = explode(',', $rgbColor);

    expect(fn (): mixed => $colority->fromRgb($rgbColor))->not()->toThrow(InvalidArgumentException::class, 'Unknown or invalid value color');

    expect($colority->fromRgb($rgbColor))->toBeInstanceOf(RgbColor::class);

})->with(['255,255,255', 'rgb(0,0,0)']);

test('fromRgb() with invalid value color throws InvalidArgumentException', function (string $rgbValue): void {
    $colority = ColorityManager::instance();

    expect(fn (): mixed => $colority->fromRgb($rgbValue))->toThrow(InvalidArgumentException::class, 'Unknown or invalid value color');

    expect($colority->parse($rgbValue))->toBeNull();

})->with(['rgba(0,0,0)', 'rgb(t,o,m)', 'rgb(-255,-255,-255)', '0,0,0,0', 'rgb(0,0,0,0)']);

/**
 * HSL
 */
test('fromHsl() with right value color returns HslColor instance', function (string $hslColor): void {
    $colority = ColorityManager::instance();

    expect(fn (): mixed => $colority->fromHsl($hslColor))->not()->toThrow(InvalidArgumentException::class, 'Unknown or invalid value color');

    expect($colority->fromHsl($hslColor))->toBeInstanceOf(HslColor::class);

    expect($colority->parse($hslColor))->toBeInstanceOf(HslColor::class);

})->with(['hsl(0,0,0%)', 'hsl(200,50%,50%)', 'hsl(0deg,0%,0%)', 'hsl(125, 20, 20)', 'hsl(32.4deg,60.48%,51.37%)', 'hsl(32.4,60.48,51.37)', 'hsl(168.31deg, 49.58%, 46.67%)', '168.31, 49.58, 46.67']);

test('fromHsl() with array right value color returns HslColor instance', function (string $hslColor): void {
    $colority = ColorityManager::instance();

    $hslColor = explode(',', $hslColor);

    expect(fn (): mixed => $colority->fromHsl($hslColor))->not()->toThrow(InvalidArgumentException::class, 'Unknown or invalid value color');

    expect($colority->fromHsl($hslColor))->toBeInstanceOf(HslColor::class);

})->with(['167.5,52.17,27.06', '282.46,57.02,44.71', 'hsl(0,0,0)']);

test('fromHsl() with invalid value color throws InvalidArgumentException', function (string $hslColor): void {
    $colority = ColorityManager::instance();

    expect(fn (): mixed => $colority->fromHsl($hslColor))->toThrow(InvalidArgumentException::class, 'Unknown or invalid value color');

    expect($colority->parse($hslColor))->toBeNull();

})->with(['xxx(0,0,0)', 't,o,m', '(-255,-255,-255)', '0,0,0,0', 'hsl(0,0,0,0)']);
