<?php

declare(strict_types=1);

use Tomloprod\Colority\Support\Parsers\HexValueColorParser;

test('getRegex matches valid hex color strings', function (string $valueColor): void {
    expect(preg_match(HexValueColorParser::getRegex(), $valueColor))->toBe(1);
})->with([
    '#000', '#FFF', '#abc',
    '#000000', '#FFFFFF', '#21695A',
]);

test('getRegex rejects invalid hex color strings', function (string $valueColor): void {
    expect(preg_match(HexValueColorParser::getRegex(), $valueColor))->toBe(0);
})->with([
    '000000',
    '#GGGGGG',
    '#1234',
    '#12345',
    '#1234567',
    'rgb(0,0,0)',
]);
