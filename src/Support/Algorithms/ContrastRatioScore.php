<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Support\Algorithms;

enum ContrastRatioScore: string
{
    case Excellent = 'Excellent';

    case Good = 'Good';

    case Acceptable = 'Acceptable';

    case Insufficient = 'Insufficient';

    // @codeCoverageIgnoreStart
    /**
     * @param  bool  $largeText  font size >= 14pt (18.66px) and bold || >= 18pt (24px) or larger.
     */
    public static function passesTextAALevel(float $contrastRatio, bool $largeText = false): bool
    {
        $minimunScore = ($largeText) ? self::Acceptable->getMinimumScore() : self::Good->getMinimumScore();

        return $contrastRatio >= $minimunScore;
    }

    /**
     * @param  bool  $largeText  font size >= 14pt (18.66px) and bold || >= 18pt (24px) or larger.
     */
    public static function passesTextAAALevel(float $contrastRatio, bool $largeText = false): bool
    {
        $minimunScore = ($largeText) ? self::Good->getMinimumScore() : self::Excellent->getMinimumScore();

        return $contrastRatio >= $minimunScore;
    }

    /**
     * Used for Graphical Objects and User Interface Components (input texts, icons, ...)
     */
    public static function passesUIAALevel(float $contrastRatio): bool
    {
        return $contrastRatio >= self::Acceptable->getMinimumScore();
    }

    public function getMinimumScore(): float
    {
        return match ($this) {
            // greater or equal than 7
            ContrastRatioScore::Excellent => 7,

            // greater or equal than 4.5
            ContrastRatioScore::Good => 4.5,

            // greater or equal than 3
            ContrastRatioScore::Acceptable => 3,

            // less than 3
            ContrastRatioScore::Insufficient => 0,
        };
    }
    // @codeCoverageIgnoreEnd
}
