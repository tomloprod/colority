<?php

declare(strict_types=1);

use Tomloprod\Colority\Colors\Color;
use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\OklchColor;
use Tomloprod\Colority\Colors\RgbColor;
use Tomloprod\Colority\Support\GradientResolver;
use Tomloprod\Colority\Support\ValueColorParserResolver;

test('isGradient detects the three CSS gradient functions', function (string $valueColor): void {
    expect(GradientResolver::isGradient($valueColor))->toBeTrue();
})->with([
    'linear-gradient(red, blue)',
    'radial-gradient(red, blue)',
    'conic-gradient(red, blue)',
    'LINEAR-GRADIENT(red, blue)',
    'linear-gradient(135deg, rgb(0,0,0) 0%, rgb(255,255,255) 100%) !important',
]);

test('isGradient rejects non-gradient strings', function (string $valueColor): void {
    expect(GradientResolver::isGradient($valueColor))->toBeFalse();
})->with([
    '#FF0000',
    'rgb(255,0,0)',
    'hsl(0,100,50)',
    'oklch(0.5 0.1 180)',
    'not-a-color',
    '',
]);

test('resolveDominant returns the first stop on a 50/50 two-stop gradient', function (): void {
    $resolver = new GradientResolver(
        fn (string $s): ?Color => (new ValueColorParserResolver())->parse($s),
    );

    $color = $resolver->resolveDominant(
        'linear-gradient(135deg, rgb(17, 153, 142) 0%, rgb(56, 239, 125) 100%)'
    );

    expect($color)->toBeInstanceOf(RgbColor::class);
    expect($color?->getValueColor())->toBe('rgb(17,153,142)');
});

test('resolveDominant strips !important', function (): void {
    $resolver = new GradientResolver(
        fn (string $s): ?Color => (new ValueColorParserResolver())->parse($s),
    );

    $color = $resolver->resolveDominant(
        'linear-gradient(135deg, rgb(17, 153, 142) 0%, rgb(56, 239, 125) 100%) !important'
    );

    expect($color)->toBeInstanceOf(RgbColor::class);
    expect($color?->getValueColor())->toBe('rgb(17,153,142)');
});

test('resolveDominant picks the stop with the widest visual area', function (): void {
    $resolver = new GradientResolver(
        fn (string $s): ?Color => (new ValueColorParserResolver())->parse($s),
    );

    // red 0-15, blue 15-65 (widest), green 65-100
    $color = $resolver->resolveDominant(
        'linear-gradient(rgb(255,0,0) 0%, rgb(0,0,255) 30%, rgb(0,255,0) 100%)'
    );

    expect($color?->getValueColor())->toBe('rgb(0,0,255)');
});

test('resolveDominant distributes positions evenly when not declared', function (): void {
    $resolver = new GradientResolver(
        fn (string $s): ?Color => (new ValueColorParserResolver())->parse($s),
    );

    // No positions: red=0, blue=50, green=100. blue dominates 25-75 (width 50).
    $color = $resolver->resolveDominant(
        'linear-gradient(rgb(255,0,0), rgb(0,0,255), rgb(0,255,0))'
    );

    expect($color?->getValueColor())->toBe('rgb(0,0,255)');
});

test('resolveDominant skips angle and shape metadata segments', function (string $valueColor, string $expectedRgb): void {
    $resolver = new GradientResolver(
        fn (string $s): ?Color => (new ValueColorParserResolver())->parse($s),
    );

    $color = $resolver->resolveDominant($valueColor);

    expect($color?->getValueColor())->toBe($expectedRgb);
})->with([
    ['linear-gradient(135deg, rgb(255,0,0) 0%, rgb(0,255,0) 100%)', 'rgb(255,0,0)'],
    ['radial-gradient(circle, rgb(255,0,0) 0%, rgb(0,255,0) 100%)', 'rgb(255,0,0)'],
    ['conic-gradient(from 90deg, rgb(255,0,0) 0%, rgb(0,255,0) 100%)', 'rgb(255,0,0)'],
]);

test('resolveDominant supports mixed color formats inside one gradient', function (): void {
    $resolver = new GradientResolver(
        fn (string $s): ?Color => (new ValueColorParserResolver())->parse($s),
    );

    // hex 0%, oklch 60% (widest range 30-80), rgb 100%
    $color = $resolver->resolveDominant(
        'linear-gradient(#FF0000 0%, oklch(0.5 0.1 180) 60%, rgb(0,0,255) 100%)'
    );

    expect($color)->toBeInstanceOf(OklchColor::class);
});

test('resolveDominant returns the only stop on a single-stop gradient', function (): void {
    $resolver = new GradientResolver(
        fn (string $s): ?Color => (new ValueColorParserResolver())->parse($s),
    );

    $color = $resolver->resolveDominant('linear-gradient(rgb(33,105,90))');

    expect($color)->toBeInstanceOf(RgbColor::class);
    expect($color?->getValueColor())->toBe('rgb(33,105,90)');
});

test('resolveDominant returns null on a gradient with no parseable stops', function (): void {
    $resolver = new GradientResolver(
        fn (string $s): ?Color => (new ValueColorParserResolver())->parse($s),
    );

    expect($resolver->resolveDominant('linear-gradient(red, blue, green)'))->toBeNull();
});

test('resolveDominant returns null when the input is not a gradient', function (): void {
    $resolver = new GradientResolver(
        fn (string $s): ?Color => (new ValueColorParserResolver())->parse($s),
    );

    expect($resolver->resolveDominant('rgb(255,0,0)'))->toBeNull();
});

test('parse() on the resolver delegates to GradientResolver for gradient strings', function (): void {
    $color = (new ValueColorParserResolver())->parse(
        'linear-gradient(135deg, rgb(17, 153, 142) 0%, rgb(56, 239, 125) 100%) !important'
    );

    expect($color)->toBeInstanceOf(RgbColor::class);
    expect($color?->getValueColor())->toBe('rgb(17,153,142)');
});

test('parse() on the resolver still parses single colors', function (): void {
    $color = (new ValueColorParserResolver())->parse('#FF0000');

    expect($color)->toBeInstanceOf(HexColor::class);
    expect($color?->getValueColor())->toBe('#FF0000');
});
