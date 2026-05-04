<?php

declare(strict_types=1);

use Tomloprod\Colority\Support\Parsers\HslValueColorParser;

test('getRegex matches valid hsl color strings', function (string $valueColor): void {
    expect(preg_match(HslValueColorParser::getRegex(), $valueColor))->toBe(1);
})->with([
    'hsl(0,0,0)',
    'hsl(180,50,50)',
    'hsl(360,100,100)',
    'hsl(167.5,52.17,27.06)',
]);

test('getRegex rejects invalid hsl color strings', function (string $valueColor): void {
    expect(preg_match(HslValueColorParser::getRegex(), $valueColor))->toBe(0);
})->with([
    'hsl(361,50,50)',
    'hsl(0,101,50)',
    'hsl(0,50,101)',
    'hsl(0,50)',
    'rgb(0,0,0)',
]);
