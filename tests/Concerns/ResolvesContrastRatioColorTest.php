<?php

declare(strict_types=1);

use Tomloprod\Colority\Colors\Color;
use Tomloprod\Colority\Colors\HexColor;

test('getContrastRatio with #000000 foreground', function (string $hexColor, float $contrastRatioWCAG): void {
    /** @var float $contrastRatio */
    $contrastRatio = (new HexColor($hexColor))->getContrastRatio(new HexColor('#000000'));

    expect($contrastRatio)->toBe($contrastRatioWCAG);
})->with([
    ['#FFFFFF', 21],
    ['#ABC841', 11.06],
    ['#4BD396', 11.05],
    ['#B9B6B6', 10.42],
    ['#EDA02A', 9.66],
    ['#ABABAB', 9.14],
    ['#5B7A80', 4.54],
    ['#323433', 1.67],
    ['#161817', 1.17],
    ['#000000', 1],
]);

test('getContrastRatio with #FFFFFF foreground', function (string $hexColor, float $contrastRatioWCAG): void {
    /** @var float $contrastRatio */
    $contrastRatio = (new HexColor($hexColor))->getContrastRatio(new HexColor('#FFFFFF'));

    expect($contrastRatio)->toBe($contrastRatioWCAG);
})->with([
    ['#000000', 21],
    ['#441273', 13.29],
    ['#592B88', 9.87],
    ['#7B4C4C', 7.04],
    ['#327E16', 5.08],
    ['#BD4747', 5.05],
    ['#454C42', 8.87],
    ['#857297', 4.32],
    ['#D4E5CC', 1.32],
    ['#FFFFFF', 1],
]);

test('getContrastRatio with default foreground', function (string $hexColor, float $contrastRatioWCAG): void {
    /** @var float $contrastRatio */
    $contrastRatio = (new HexColor($hexColor))->getContrastRatio();

    expect($contrastRatio)->toBe($contrastRatioWCAG);
})->with([
    ['#FFFFFF', 21],
    ['#ABC841', 11.06],
    ['#4BD396', 11.05],
    ['#B9B6B6', 10.42],
    ['#EDA02A', 9.66],
    ['#ABABAB', 9.14],
    ['#5B7A80', 4.54],
    ['#323433', 1.67],
    ['#161817', 1.17],
    ['#000000', 1],
]);

test('getBestForegroundColor with #000000 background', function (): void {
    $hexColor = new HexColor('#000000');

    /** @var Color $bestForegroundColor */
    $bestForegroundColor = $hexColor->getBestForegroundColor([
        new HexColor('#000000'),
        new HexColor('#441273'),
        new HexColor('#592B88'),
        new HexColor('#7B4C4C'),
        new HexColor('#327E16'),
        new HexColor('#BD4747'),
        new HexColor('#454C42'),
        new HexColor('#857297'),
        new HexColor('#D4E5CC'),
        new HexColor('#FFFFFF'),
    ]);

    expect($bestForegroundColor->getValueColor())->toBe('#FFFFFF');
});

test('getBestForegroundColor with #FFFFFF background', function (): void {
    $hexColor = new HexColor('#FFFFFF');

    /** @var Color $bestForegroundColor */
    $bestForegroundColor = $hexColor->getBestForegroundColor([
        new HexColor('#000000'),
        new HexColor('#441273'),
        new HexColor('#592B88'),
        new HexColor('#7B4C4C'),
        new HexColor('#327E16'),
        new HexColor('#BD4747'),
        new HexColor('#454C42'),
        new HexColor('#857297'),
        new HexColor('#D4E5CC'),
        new HexColor('#FFFFFF'),
    ]);

    expect($bestForegroundColor->getValueColor())->toBe('#000000');
});

test('getBestForegroundColor with #FFFFFF background and default foregrounds', function (): void {
    $hexColor = new HexColor('#FFFFFF');

    /** @var Color $bestForegroundColor */
    $bestForegroundColor = $hexColor->getBestForegroundColor();

    expect($bestForegroundColor->getValueColor())->toBe('#000000');
});

test('getBestForegroundColor with #000000 background and default foregrounds', function (): void {
    $hexColor = new HexColor('#000000');

    /** @var Color $bestForegroundColor */
    $bestForegroundColor = $hexColor->getBestForegroundColor();

    expect($bestForegroundColor->getValueColor())->toBe('#FFFFFF');
});
