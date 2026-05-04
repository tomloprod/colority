<?php

declare(strict_types=1);

use Tomloprod\Colority\Support\Parsers\RgbValueColorParser;

test('getRegex matches valid rgb color strings', function (string $valueColor): void {
    expect(preg_match(RgbValueColorParser::getRegex(), $valueColor))->toBe(1);
})->with([
    'rgb(0,0,0)',
    'rgb(255,255,255)',
    'rgb(33,105,90)',
    'rgb(141,49,179)',
]);

test('getRegex rejects invalid rgb color strings', function (string $valueColor): void {
    expect(preg_match(RgbValueColorParser::getRegex(), $valueColor))->toBe(0);
})->with([
    'rgb(256,0,0)',
    'rgb(-1,0,0)',
    'rgb(0,0)',
    'rgb(0, 0, 0)',
    'rgba(0,0,0,1)',
    'hsl(0,0,0)',
]);
