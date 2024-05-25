<?php

declare(strict_types=1);

use Tomloprod\Colority\Support\Algorithms\LuminosityContrastRatio;

test('getContrastRatio white and black', function (): void {
    $lumContrastRatio = new LuminosityContrastRatio();

    /** @var float $contrastRatio */
    $contrastRatio = $lumContrastRatio->getContrastRatio([255, 255, 255], [0, 0, 0]);

    expect($contrastRatio)->toBeGreaterThanOrEqual(7);
});

test('getContrastRatio white and white', function (): void {
    $lumContrastRatio = new LuminosityContrastRatio();

    /** @var float $contrastRatio */
    $contrastRatio = $lumContrastRatio->getContrastRatio([255, 255, 255], [255, 255, 255]);

    expect($contrastRatio)->toBeLessThan(3);
});

test('getContrastRatio black and black', function (): void {
    $lumContrastRatio = new LuminosityContrastRatio();

    /** @var float $contrastRatio */
    $contrastRatio = $lumContrastRatio->getContrastRatio([0, 0, 0], [0, 0, 0]);

    expect($contrastRatio)->toBeLessThan(3);
});
