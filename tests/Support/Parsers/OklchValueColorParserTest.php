<?php

declare(strict_types=1);

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
]);
