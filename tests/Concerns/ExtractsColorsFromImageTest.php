<?php

declare(strict_types=1);

use Tomloprod\Colority\Support\Dtos\ImageColorFrequency;

define('FIXTURES_PATH', dirname(__DIR__).'/Fixtures');

test('getImageMostCommonColor returns the most frequent color', function (): void {
    // dominant-colors.png: 50% red, 30% blue, 20% green
    $color = colority()->getImageMostCommonColor(FIXTURES_PATH.'/dominant-colors.png');

    expect($color->toHex()->getValueColor())->toBe('#FF0000');
});

test('getImageDominantColors returns colors with frequency metadata', function (): void {
    // dominant-colors.png: 50% red, 30% blue, 20% green
    $frequencies = colority()->getImageDominantColors(FIXTURES_PATH.'/dominant-colors.png', 3);

    expect($frequencies)->toHaveCount(3);
    expect($frequencies[0])->toBeInstanceOf(ImageColorFrequency::class);

    // First should be red (50%)
    expect($frequencies[0]->color->toHex()->getValueColor())->toBe('#FF0000');
    expect($frequencies[0]->percentage)->toBeGreaterThan(45.0);
    expect($frequencies[0]->pixelCount)->toBeGreaterThan(0);

    // Second should be blue (30%)
    expect($frequencies[1]->color->toHex()->getValueColor())->toBe('#0000FF');
    expect($frequencies[1]->percentage)->toBeGreaterThan(25.0);

    // Third should be green (20%)
    expect($frequencies[2]->color->toHex()->getValueColor())->toBe('#00FF00');
    expect($frequencies[2]->percentage)->toBeGreaterThan(15.0);
});

test('getImageDominantColors works with different color distributions', function (): void {
    // dominant-colors-alt.png: 60% yellow, 25% cyan, 15% magenta
    $frequencies = colority()->getImageDominantColors(FIXTURES_PATH.'/dominant-colors-alt.png', 3);

    expect($frequencies)->toHaveCount(3);

    // First should be yellow (60%)
    expect($frequencies[0]->color->toHex()->getValueColor())->toBe('#FFFF00');
    expect($frequencies[0]->percentage)->toBeGreaterThan(55.0);

    // Second should be cyan (25%)
    expect($frequencies[1]->color->toHex()->getValueColor())->toBe('#00FFFF');
    expect($frequencies[1]->percentage)->toBeGreaterThan(20.0);

    // Third should be magenta (15%)
    expect($frequencies[2]->color->toHex()->getValueColor())->toBe('#FF00FF');
    expect($frequencies[2]->percentage)->toBeGreaterThan(10.0);
});

test('getImageColors can obtain image colors', function (): void {
    $imageColors = colority()->getImageColors(FIXTURES_PATH.'/image-colors.png');

    $hexImageColors = [];
    foreach ($imageColors as $imageColor) {
        $hexImageColors[] = $imageColor->toHex()->getValueColor();
    }

    foreach (['#FF0101', '#18FF01', '#014BFF', '#FFCB01'] as $hexColor) {
        expect(in_array($hexColor, $hexImageColors))->toBeTrue();
    }
});
