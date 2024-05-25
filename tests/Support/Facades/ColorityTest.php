<?php

declare(strict_types=1);

use Tomloprod\Colority\Services\ColorityManager;
use Tomloprod\Colority\Support\Facades\Colority;

test('facade returns the same instance', function (): void {
    $instance1 = ColorityManager::instance();
    $instance2 = Colority::instance();

    expect($instance1)->toBe($instance2);
});
