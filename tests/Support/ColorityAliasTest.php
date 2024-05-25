<?php

declare(strict_types=1);

use Tomloprod\Colority\Services\ColorityManager;

test('colority alias return instance of colority', function (): void {
    expect(colority())->toBeInstanceOf(ColorityManager::class);
});
