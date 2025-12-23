<?php

declare(strict_types=1);

use Tomloprod\Colority\Colors\HexColor;
use Tomloprod\Colority\Colors\HslColor;
use Tomloprod\Colority\Colors\RgbColor;

test('isEqualTo() returns true for identical HexColor instances', function (): void {
    $hexColor1 = new HexColor('#FF0000');
    $hexColor2 = new HexColor('#FF0000');

    expect($hexColor1->isEqualTo($hexColor2))->toBeTrue();
    expect($hexColor2->isEqualTo($hexColor1))->toBeTrue();
});

test('isEqualTo() returns true for identical RgbColor instances', function (): void {
    $rgbColor1 = new RgbColor('rgb(255,0,0)');
    $rgbColor2 = new RgbColor('rgb(255,0,0)');

    expect($rgbColor1->isEqualTo($rgbColor2))->toBeTrue();
    expect($rgbColor2->isEqualTo($rgbColor1))->toBeTrue();
});

test('isEqualTo() returns true for identical HslColor instances', function (): void {
    $hslColor1 = new HslColor('hsl(0,100,50)');
    $hslColor2 = new HslColor('hsl(0,100,50)');

    expect($hslColor1->isEqualTo($hslColor2))->toBeTrue();
    expect($hslColor2->isEqualTo($hslColor1))->toBeTrue();
});

test('isEqualTo() returns true when comparing equivalent colors of different types', function (): void {
    $hexColor = new HexColor('#FF0000');
    $rgbColor = new RgbColor('rgb(255,0,0)');
    $hslColor = new HslColor('hsl(0,100,50)');

    // HexColor vs RgbColor
    expect($hexColor->isEqualTo($rgbColor))->toBeTrue();
    expect($rgbColor->isEqualTo($hexColor))->toBeTrue();

    // HexColor vs HslColor
    expect($hexColor->isEqualTo($hslColor))->toBeTrue();
    expect($hslColor->isEqualTo($hexColor))->toBeTrue();

    // RgbColor vs HslColor
    expect($rgbColor->isEqualTo($hslColor))->toBeTrue();
    expect($hslColor->isEqualTo($rgbColor))->toBeTrue();
});

test('isEqualTo() returns false for different colors', function (): void {
    $redHex = new HexColor('#FF0000');
    $blueHex = new HexColor('#0000FF');

    expect($redHex->isEqualTo($blueHex))->toBeFalse();
    expect($blueHex->isEqualTo($redHex))->toBeFalse();
});

test('isEqualTo() returns false for different colors of different types', function (): void {
    $redHex = new HexColor('#FF0000');
    $blueRgb = new RgbColor('rgb(0,0,255)');
    $greenHsl = new HslColor('hsl(120,100,50)');

    expect($redHex->isEqualTo($blueRgb))->toBeFalse();
    expect($redHex->isEqualTo($greenHsl))->toBeFalse();
    expect($blueRgb->isEqualTo($greenHsl))->toBeFalse();
});

test('isEqualTo() returns true for HexColor with different case input', function (): void {
    $upperCaseHex = new HexColor('#FF0000');
    $lowerCaseHex = new HexColor('#ff0000');

    expect($upperCaseHex->isEqualTo($lowerCaseHex))->toBeTrue();
});

test('isEqualTo() returns true for same color instance', function (): void {
    $hexColor = new HexColor('#FF0000');

    expect($hexColor->isEqualTo($hexColor))->toBeTrue();
});

test('isEqualTo() handles black color across all types', function (): void {
    $blackHex = new HexColor('#000000');
    $blackRgb = new RgbColor('rgb(0,0,0)');
    $blackHsl = new HslColor('hsl(0,0,0)');

    expect($blackHex->isEqualTo($blackRgb))->toBeTrue();
    expect($blackHex->isEqualTo($blackHsl))->toBeTrue();
    expect($blackRgb->isEqualTo($blackHsl))->toBeTrue();
});

test('isEqualTo() handles white color across all types', function (): void {
    $whiteHex = new HexColor('#FFFFFF');
    $whiteRgb = new RgbColor('rgb(255,255,255)');
    $whiteHsl = new HslColor('hsl(0,0,100)');

    expect($whiteHex->isEqualTo($whiteRgb))->toBeTrue();
    expect($whiteHex->isEqualTo($whiteHsl))->toBeTrue();
    expect($whiteRgb->isEqualTo($whiteHsl))->toBeTrue();
});

test('getLuminance() returns correct values for known colors', function (): void {
    $black = new HexColor('#000000');
    $white = new HexColor('#FFFFFF');
    $red = new HexColor('#FF0000');

    expect($black->getLuminance())->toBe(0.0);
    expect($white->getLuminance())->toBe(1.0);
    expect($red->getLuminance())->toBeGreaterThan(0.2);
    expect($red->getLuminance())->toBeLessThan(0.3);
});

test('isDark() returns true for dark colors', function (): void {
    $black = new HexColor('#000000');
    $darkGray = new HexColor('#333333');
    $darkBlue = new HexColor('#000080');

    expect($black->isDark())->toBeTrue();
    expect($darkGray->isDark())->toBeTrue();
    expect($darkBlue->isDark())->toBeTrue();
});

test('isDark() returns false for light colors', function (): void {
    $white = new HexColor('#FFFFFF');
    $lightGray = new HexColor('#CCCCCC');
    $yellow = new HexColor('#FFFF00');

    expect($white->isDark())->toBeFalse();
    expect($lightGray->isDark())->toBeFalse();
    expect($yellow->isDark())->toBeFalse();
});

test('isLight() returns true for light colors', function (): void {
    $white = new HexColor('#FFFFFF');
    $lightGray = new HexColor('#CCCCCC');
    $yellow = new HexColor('#FFFF00');

    expect($white->isLight())->toBeTrue();
    expect($lightGray->isLight())->toBeTrue();
    expect($yellow->isLight())->toBeTrue();
});

test('isLight() returns false for dark colors', function (): void {
    $black = new HexColor('#000000');
    $darkGray = new HexColor('#333333');
    $darkBlue = new HexColor('#000080');

    expect($black->isLight())->toBeFalse();
    expect($darkGray->isLight())->toBeFalse();
    expect($darkBlue->isLight())->toBeFalse();
});

test('isDark() and isLight() are mutually exclusive', function (): void {
    $colors = [
        new HexColor('#000000'),
        new HexColor('#FFFFFF'),
        new HexColor('#FF0000'),
        new HexColor('#00FF00'),
        new HexColor('#0000FF'),
        new HexColor('#808080'),
    ];

    foreach ($colors as $color) {
        expect($color->isDark() !== $color->isLight())->toBeTrue();
    }
});

test('isDarkerThan() correctly compares colors', function (): void {
    $black = new HexColor('#000000');
    $gray = new HexColor('#808080');
    $white = new HexColor('#FFFFFF');

    expect($black->isDarkerThan($gray))->toBeTrue();
    expect($black->isDarkerThan($white))->toBeTrue();
    expect($gray->isDarkerThan($white))->toBeTrue();

    expect($white->isDarkerThan($gray))->toBeFalse();
    expect($white->isDarkerThan($black))->toBeFalse();
    expect($gray->isDarkerThan($black))->toBeFalse();
});

test('isLighterThan() correctly compares colors', function (): void {
    $black = new HexColor('#000000');
    $gray = new HexColor('#808080');
    $white = new HexColor('#FFFFFF');

    expect($white->isLighterThan($gray))->toBeTrue();
    expect($white->isLighterThan($black))->toBeTrue();
    expect($gray->isLighterThan($black))->toBeTrue();

    expect($black->isLighterThan($gray))->toBeFalse();
    expect($black->isLighterThan($white))->toBeFalse();
    expect($gray->isLighterThan($white))->toBeFalse();
});

test('isDarkerThan() and isLighterThan() work across color types', function (): void {
    $darkHex = new HexColor('#333333');
    $lightRgb = new RgbColor('rgb(200,200,200)');
    $mediumHsl = new HslColor('hsl(0,0,50)');

    expect($darkHex->isDarkerThan($lightRgb))->toBeTrue();
    expect($lightRgb->isLighterThan($darkHex))->toBeTrue();
    expect($darkHex->isDarkerThan($mediumHsl))->toBeTrue();
    expect($mediumHsl->isLighterThan($darkHex))->toBeTrue();
});

test('isDarkerThan() and isLighterThan() return false for equal luminance', function (): void {
    $color1 = new HexColor('#FF0000');
    $color2 = new HexColor('#FF0000');

    expect($color1->isDarkerThan($color2))->toBeFalse();
    expect($color1->isLighterThan($color2))->toBeFalse();
});
