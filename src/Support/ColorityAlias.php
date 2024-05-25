<?php

declare(strict_types=1);

use Tomloprod\Colority\Services\ColorityManager;

if (! function_exists('colority')) {
    function colority(): ColorityManager
    {
        return ColorityManager::instance();
    }
}
