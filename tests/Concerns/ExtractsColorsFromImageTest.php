<?php

declare(strict_types=1);

it('getImageMostCommonColor -> fixed image with color pixels')->todo();

test('getImageColors can obtain image colors', function (): void {

    $imageColors = colority()->getImageColors(__DIR__.'/image-colors.png');

    $hexImageColors = [];
    foreach ($imageColors as $imageColor) {
        $hexImageColors[] = $imageColor->toHex()->getValueColor();
    }

    foreach (['#FF0101', '#18FF01', '#014BFF', '#FFCB01'] as $hexColor) {
        expect(in_array($hexColor, $hexImageColors))->toBeTrue();
    }
});
