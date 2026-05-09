<?php

declare(strict_types=1);

use Tomloprod\Colority\Colors\Color;
use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Colors\OklchColor;
use Tomloprod\Colority\Colors\RgbColor;
use Tomloprod\Colority\Services\ColorityManager;

beforeEach(function (): void {
    ColorityManager::instance()->resetPalette();
});

afterEach(function (): void {
    ColorityManager::instance()->resetPalette();
});

test('default palette exposes the eight semantic slots', function (): void {
    $manager = ColorityManager::instance();

    expect($manager->primary())->toBeInstanceOf(Color::class);
    expect($manager->secondary())->toBeInstanceOf(Color::class);
    expect($manager->success())->toBeInstanceOf(Color::class);
    expect($manager->danger())->toBeInstanceOf(Color::class);
    expect($manager->warning())->toBeInstanceOf(Color::class);
    expect($manager->info())->toBeInstanceOf(Color::class);
    expect($manager->light())->toBeInstanceOf(Color::class);
    expect($manager->dark())->toBeInstanceOf(Color::class);
});

test('default palette returns the documented hex values', function (
    string $slot,
    string $expectedHex,
): void {
    $color = ColorityManager::instance()->color($slot);

    expect($color)->toBeInstanceOf(Color::class);
    expect($color?->toHex()->getValueColor())->toBe($expectedHex);
})->with([
    ['primary', '#5B8DEF'],
    ['secondary', '#A1A8B3'],
    ['success', '#4BD396'],
    ['danger', '#F5707A'],
    ['warning', '#F9C851'],
    ['info', '#3AC9D6'],
    ['light', '#F4F6F8'],
    ['dark', '#1F2937'],
]);

test('color() returns null for unknown slot names', function (): void {
    expect(ColorityManager::instance()->color('does-not-exist'))->toBeNull();
});

test('named setters update the corresponding slot', function (
    string $setter,
    string $getter,
): void {
    $manager = ColorityManager::instance();

    $manager->{$setter}('#FF6347');

    expect($manager->{$getter}()->toHex()->getValueColor())->toBe('#FF6347');
})->with([
    ['setPrimary', 'primary'],
    ['setSecondary', 'secondary'],
    ['setSuccess', 'success'],
    ['setDanger', 'danger'],
    ['setWarning', 'warning'],
    ['setInfo', 'info'],
    ['setLight', 'light'],
    ['setDark', 'dark'],
]);

test('named setters accept Color instances and preserve the type', function (): void {
    $manager = ColorityManager::instance();

    $manager->setPrimary(new RgbColor('rgb(255, 99, 71)'));

    expect($manager->primary())->toBeInstanceOf(RgbColor::class);
    expect($manager->primary()->toHex()->getValueColor())->toBe('#FF6347');
});

test('setters accept any string format the resolver understands', function (
    string $input,
    string $expectedClass,
    string $expectedCanonical,
): void {
    $manager = ColorityManager::instance();

    $manager->setPrimary($input);

    expect($manager->primary())->toBeInstanceOf($expectedClass);
    expect($manager->primary()->getValueColor())->toBe($expectedCanonical);
})->with([
    'hex' => ['#FF6347', HexColor::class, '#FF6347'],
    'rgb' => ['rgb(255, 99, 71)', RgbColor::class, 'rgb(255,99,71)'],
    'hsl' => ['hsl(9, 100%, 64%)', HslColor::class, 'hsl(9,100,64)'],
    'oklch' => ['oklch(0.7 0.18 30)', OklchColor::class, 'oklch(0.7 0.18 30)'],
    'gradient' => [
        'linear-gradient(135deg, #FF6347 0%, #00CED1 100%)',
        HexColor::class,
        '#FF6347',
    ],
]);

test('setters throw InvalidArgumentException for unparseable strings', function (): void {
    expect(fn (): mixed => ColorityManager::instance()->setPrimary('not-a-color'))
        ->toThrow(InvalidArgumentException::class, 'Unknown or invalid value color');
});

test('setColor stores arbitrary slot names retrievable via color()', function (): void {
    $manager = ColorityManager::instance();

    $manager->setColor('brand', '#0D6EFD');

    expect($manager->color('brand'))->toBeInstanceOf(Color::class);
    expect($manager->color('brand')?->toHex()->getValueColor())->toBe('#0D6EFD');
});

test('configure overwrites multiple slots at once', function (): void {
    $manager = ColorityManager::instance();

    $manager->configure([
        'primary' => '#FF6347',
        'success' => '#00CED1',
        'brand' => '#0D6EFD',
    ]);

    expect($manager->primary()->toHex()->getValueColor())->toBe('#FF6347');
    expect($manager->success()->toHex()->getValueColor())->toBe('#00CED1');
    expect($manager->color('brand')?->toHex()->getValueColor())->toBe('#0D6EFD');
});

test('configure preserves slots that are not overridden', function (): void {
    $manager = ColorityManager::instance();
    $originalDanger = $manager->danger()->toHex()->getValueColor();

    $manager->configure(['primary' => '#FF6347']);

    expect($manager->danger()->toHex()->getValueColor())->toBe($originalDanger);
});

test('resetPalette restores the documented defaults', function (): void {
    $manager = ColorityManager::instance();

    $manager->setPrimary('#FF6347');
    $manager->resetPalette();

    expect($manager->primary())->toBeInstanceOf(HexColor::class);
    expect($manager->primary()->toHex()->getValueColor())->toBe('#5B8DEF');
});

test('buildPalette sets primary to the brand color', function (): void {
    $manager = ColorityManager::instance();

    $manager->buildPalette('#FF6347');

    expect($manager->primary()->toHex()->getValueColor())->toBe('#FF6347');
});

test('buildPalette derives secondary as the complementary hue with reduced saturation', function (): void {
    $manager = ColorityManager::instance();

    // Brand: HSL(9, 100%, 64%) -> complement at H=189, S clamped to 40
    $manager->buildPalette(new HslColor([9, 100, 64]));

    [$h, $s, $l] = $manager->secondary()->toHsl()->getArrayValueColor();

    expect($h)->toBe(189.0);
    expect($s)->toBe(40.0);
    expect($l)->toBe(64.0);
});

test('buildPalette derives light and dark from the brand', function (): void {
    $manager = ColorityManager::instance();

    $manager->buildPalette(new HslColor([200, 50, 50]));

    expect($manager->light()->toHsl()->getArrayValueColor()[2])->toBe(90.0);
    expect($manager->dark()->toHsl()->getArrayValueColor()[2])->toBe(10.0);
});

test('buildPalette keeps the semantic defaults for success, danger, warning and info', function (): void {
    $manager = ColorityManager::instance();

    $manager->buildPalette('#FF6347');

    expect($manager->success()->toHex()->getValueColor())->toBe('#4BD396');
    expect($manager->danger()->toHex()->getValueColor())->toBe('#F5707A');
    expect($manager->warning()->toHex()->getValueColor())->toBe('#F9C851');
    expect($manager->info()->toHex()->getValueColor())->toBe('#3AC9D6');
});

test('buildPalette accepts any string format the resolver understands', function (): void {
    $manager = ColorityManager::instance();

    $manager->buildPalette('rgb(255, 99, 71)');

    expect($manager->primary())->toBeInstanceOf(RgbColor::class);
    expect($manager->primary()->toHex()->getValueColor())->toBe('#FF6347');
});
