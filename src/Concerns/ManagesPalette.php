<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Concerns;

use InvalidArgumentException;
use Tomloprod\Colority\Colors\Color;
use Tomloprod\Colority\Support\Palette;
use Tomloprod\Colority\Support\ValueColorParserResolver;

trait ManagesPalette
{
    private Palette $palette;

    public function primary(): Color
    {
        return $this->paletteColor('primary');
    }

    public function secondary(): Color
    {
        return $this->paletteColor('secondary');
    }

    public function success(): Color
    {
        return $this->paletteColor('success');
    }

    public function danger(): Color
    {
        return $this->paletteColor('danger');
    }

    public function warning(): Color
    {
        return $this->paletteColor('warning');
    }

    public function info(): Color
    {
        return $this->paletteColor('info');
    }

    public function light(): Color
    {
        return $this->paletteColor('light');
    }

    public function dark(): Color
    {
        return $this->paletteColor('dark');
    }

    public function color(string $name): ?Color
    {
        return $this->palette->get($name);
    }

    public function setPrimary(Color|string $color): void
    {
        $this->setColor('primary', $color);
    }

    public function setSecondary(Color|string $color): void
    {
        $this->setColor('secondary', $color);
    }

    public function setSuccess(Color|string $color): void
    {
        $this->setColor('success', $color);
    }

    public function setDanger(Color|string $color): void
    {
        $this->setColor('danger', $color);
    }

    public function setWarning(Color|string $color): void
    {
        $this->setColor('warning', $color);
    }

    public function setInfo(Color|string $color): void
    {
        $this->setColor('info', $color);
    }

    public function setLight(Color|string $color): void
    {
        $this->setColor('light', $color);
    }

    public function setDark(Color|string $color): void
    {
        $this->setColor('dark', $color);
    }

    public function setColor(string $name, Color|string $color): void
    {
        $this->palette = $this->palette->with($name, $this->normalizeColor($color));
    }

    /**
     * @param  array<string, Color|string>  $palette
     */
    public function configure(array $palette): void
    {
        $normalized = [];
        foreach ($palette as $name => $color) {
            $normalized[$name] = $this->normalizeColor($color);
        }

        $this->palette = $this->palette->withMany($normalized);
    }

    public function resetPalette(): void
    {
        $this->palette = Palette::default();
    }

    public function buildPalette(Color|string $brandColor): void
    {
        $this->palette = Palette::buildFromColor($this->normalizeColor($brandColor));
    }

    private function paletteColor(string $name): Color
    {
        /** @var Color $color */
        $color = $this->palette->get($name);

        return $color;
    }

    private function normalizeColor(Color|string $color): Color
    {
        if ($color instanceof Color) {
            return $color;
        }

        $parsed = (new ValueColorParserResolver())->parse($color);

        if (! $parsed instanceof Color) {
            throw new InvalidArgumentException('Unknown or invalid value color: '.$color);
        }

        return $parsed;
    }
}
