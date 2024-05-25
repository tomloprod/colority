<?php

declare(strict_types=1);

arch('globals')
    ->expect(['dd', 'dump', 'ray', 'die', 'var_dump', 'sleep', 'dispatch', 'dispatch_sync'])
    ->not->toBeUsed();

arch('contracts')
    ->expect('Tomloprod\Colority\Contracts')
    ->toBeInterfaces();

arch('concerns')
    ->expect('Tomloprod\Colority\Concerns')
    ->toBeTraits();
