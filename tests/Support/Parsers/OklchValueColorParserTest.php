<?php

declare(strict_types=1);

use Tomloprod\Colority\Colors\OklchColor;
use Tomloprod\Colority\Support\Parsers\OklchValueColorParser;

test('getRegex matches valid oklch color strings', function (string $valueColor): void {
    expect(preg_match(OklchValueColorParser::getRegex(), $valueColor))->toBe(1);
})->with([
    'oklch(0 0 0)',
    'oklch(1 0 360)',
    'oklch(0.5 0.1 180)',
    'oklch(0.7 0.15 120)',
]);

test('getRegex rejects invalid oklch color strings', function (string $valueColor): void {
    expect(preg_match(OklchValueColorParser::getRegex(), $valueColor))->toBe(0);
})->with([
    'oklch(2 0.1 180)',
    'oklch(0.5 2 180)',
    'oklch(0.5 0.1 400)',
    'oklch(0.5,0.1,180)',
    'oklch(0.5 0.1)',
    'rgb(0,0,0)',
    'oklch(0.5 0.1 360.5)',
]);

test('parse accepts commas, whitespace, or any combination as separators', function (string $form): void {
    expect((new OklchColor($form))->getValueColor())->toBe('oklch(0.5 0.1 180)');
})->with([
    'oklch(0.5 0.1 180)',
    'oklch(0.5,0.1,180)',
    'oklch(0.5, 0.1, 180)',
    'oklch(0.5,0.1 180)',
    '0.5 0.1 180',
    '0.5, 0.1, 180',
]);

test('parse rescales L written as percentage to the 0-1 numeric form', function (string $form): void {
    expect((new OklchColor($form))->getValueColor())->toBe('oklch(0.5 0.1 180)');
})->with([
    'oklch(50% 0.1 180)',
    'oklch(50%, 0.1, 180)',
    'oklch(50%  0.1  180)',
]);

test('parse rescales C written as percentage to the 0-0.4 numeric form', function (string $form): void {
    expect((new OklchColor($form))->getValueColor())->toBe('oklch(0.5 0.1 180)');
})->with([
    'oklch(0.5 25% 180)',
    'oklch(0.5, 25%, 180)',
    'oklch(50% 25% 180)',
    'oklch(50%, 25%, 180)',
]);

test('parse converts H angle units to degrees', function (string $form): void {
    expect((new OklchColor($form))->getValueColor())->toBe('oklch(0.5 0.1 180)');
})->with([
    'oklch(0.5 0.1 180deg)',
    'oklch(0.5 0.1 0.5turn)',
    'oklch(0.5 0.1 200grad)',
]);
