<?php

declare(strict_types=1);

it('getImageMostCommonColor -> fixed image with color pixels')->todo();

test('getImageColors can obtain image colors', function (): void {

    $imageColors = colority()->getImageColors(__DIR__.'/image-colors.png');

    $hexImageColors = [];
    foreach ($imageColors as $imageColor) {
        $hexImageColors[] = $imageColor->toHex()->getValueColor();
    }

    foreach (['#ff0101', '#18ff01', '#014bff', '#ffcb01'] as $hexColor) {
        expect(in_array($hexColor, $hexImageColors))->toBeTrue();
    }
});
