<?php

declare(strict_types=1);

namespace Tomloprod\Colority\Concerns;

use Exception;
use GdImage;
use Tomloprod\Colority\Colors\RgbColor;
use Tomloprod\Colority\Support\Dtos\ImageColorFrequency;

trait ExtractsColorsFromImage
{
    // @codeCoverageIgnoreStart

    /**
     * Returns the most common color in an image based on pixel frequency.
     */
    public function getImageMostCommonColor(string $imagePath): RgbColor
    {
        return $this->getImageDominantColors($imagePath, 1)[0]->color;
    }

    /**
     * Returns the most dominant colors in an image based on pixel frequency.
     *
     * Colors that are perceptually too similar to already-selected colors
     * are skipped based on the RGB Euclidean distance threshold.
     *
     * @param  int  $similarityThreshold  Minimum RGB distance to consider colors as distinct (0-441). Default 50.
     * @return array<ImageColorFrequency>
     */
    public function getImageDominantColors(string $imagePath, int $desiredNumColors = 5, int $similarityThreshold = 50): array
    {
        $imageColors = $this->extractColorsFromImage($imagePath);
        $totalPixels = count($imageColors);

        $similarityThreshold = max(0, min(441, $similarityThreshold));

        // Count the frequency of each color using a simple string key
        $colorFrequencies = [];

        foreach ($imageColors as $color) {
            $key = "{$color[0]},{$color[1]},{$color[2]}";

            $colorFrequencies[$key] = ($colorFrequencies[$key] ?? 0) + 1;
        }

        // Sort by frequency (most common first)
        arsort($colorFrequencies);

        $result = [];
        $selectedRgbColors = [];

        foreach ($colorFrequencies as $key => $pixelCount) {

            if (count($result) >= $desiredNumColors) {
                break;
            }

            $rgb = array_map('intval', explode(',', $key));

            // Skip if too similar to an already selected color
            if ($this->isSimilarToAny($rgb, $selectedRgbColors, $similarityThreshold)) {
                continue;
            }

            $selectedRgbColors[] = $rgb;
            $result[] = new ImageColorFrequency(
                color: $this->fromRgb($rgb),
                percentage: round(($pixelCount / $totalPixels) * 100, 2),
                pixelCount: $pixelCount,
            );
        }

        return $result;
    }

    /**
     * @return array<RgbColor>
     *
     * @throws Exception
     */
    public function getImageColors(string $imagePath, int $desiredNumColors = 5): array
    {
        $imageColors = $this->extractColorsFromImage($imagePath);

        // Remove duplicate colors
        $imageColors = array_unique($imageColors, SORT_REGULAR);

        $countImageColors = count($imageColors);

        // Limit `$desiredNumColors` to the actual number of unique colors in the image.
        if ($desiredNumColors > $countImageColors) {
            $desiredNumColors = $countImageColors;
        }

        /**
         * K-Means clustering algorithm.
         *
         * Initialize centroids with random colors from `$imageColors`
         *
         * @var array<array<int>> $centroids
         */
        $centroids = [];

        $randomIndices = array_rand($imageColors, $desiredNumColors);
        if (is_array($randomIndices)) {
            foreach ($randomIndices as $index) {
                $centroids[] = $imageColors[$index];
            }
        } else {
            $centroids[] = $imageColors[$randomIndices];
        }

        /** @var int $maxIterations */
        $maxIterations = 100;

        /** @var array<array<array<int>>> $clusters */
        $clusters = [];

        for ($iteration = 0; $iteration < $maxIterations; $iteration++) {
            /**
             * Assign each color to the nearest centroid
             */
            $clusters = array_fill(0, count($centroids), []);

            /** @var array<int> $color */
            foreach ($imageColors as $color) {
                $closestCentroidIndex = $this->getClosestCentroidIndex($color, $centroids);
                $clusters[$closestCentroidIndex][] = $color;
            }

            /**
             * Calc new centroids
             */
            $newCentroids = $this->calculateNewCentroids($clusters);

            /**
             * Check if the centroids have changed
             */
            if ($this->centroidsAreEqual($centroids, $newCentroids)) {
                break;
            }

            $centroids = $newCentroids;
        }

        // Obtain the average colors of each cluster
        $clusterColors = [];
        foreach ($clusters as $cluster) {
            $count = count($cluster);

            if ($count > 0) {
                $rTotal = 0;
                $gTotal = 0;
                $bTotal = 0;

                foreach ($cluster as $color) {
                    $rTotal += $color[0];
                    $gTotal += $color[1];
                    $bTotal += $color[2];
                }

                $clusterColors[] = $this->fromRgb(
                    [
                        (int) ($rTotal / $count),
                        (int) ($gTotal / $count),
                        (int) ($bTotal / $count),
                    ]
                );
            }
        }

        return $clusterColors;
    }

    /**
     * @param  array<int>  $rgb
     * @param  array<array<int>>  $selectedColors
     */
    private function isSimilarToAny(array $rgb, array $selectedColors, int $threshold): bool
    {
        foreach ($selectedColors as $selected) {
            $distance = sqrt(
                ($rgb[0] - $selected[0]) ** 2 +
                ($rgb[1] - $selected[1]) ** 2 +
                ($rgb[2] - $selected[2]) ** 2
            );

            if ($distance < $threshold) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<array<array<int>>>  $clusters
     * @return array<array<int>>
     */
    private function calculateNewCentroids(array $clusters): array
    {
        $centroids = [];
        foreach ($clusters as $cluster) {
            $rTotal = 0;
            $gTotal = 0;
            $bTotal = 0;
            $count = count($cluster);

            if ($count > 0) {
                foreach ($cluster as $color) {
                    $rTotal += $color[0];
                    $gTotal += $color[1];
                    $bTotal += $color[2];
                }

                $centroids[] = [
                    (int) ($rTotal / $count),
                    (int) ($gTotal / $count),
                    (int) ($bTotal / $count),
                ];
            }
        }

        return $centroids;
    }

    /**
     * @param  array<int>  $color
     * @param  array<array<int>>  $centroids
     */
    private function getClosestCentroidIndex(array $color, array $centroids): int
    {
        $minDistance = PHP_INT_MAX;
        $closestIndex = 0;
        foreach ($centroids as $index => $centroid) {

            $distance = sqrt(
                ($color[0] - $centroid[0]) ** 2 +
                ($color[1] - $centroid[1]) ** 2 +
                ($color[2] - $centroid[2]) ** 2
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closestIndex = $index;
            }
        }

        return $closestIndex;
    }

    /**
     * @param  array<array<int>>  $centroids1
     * @param  array<array<int>>  $centroids2
     */
    private function centroidsAreEqual(array $centroids1, array $centroids2): bool
    {
        foreach ($centroids1 as $index => $centroid1) {
            if ($centroid1 !== $centroids2[$index]) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<array<int>>
     */
    private function extractColorsFromImage(string $imagePath)
    {
        if (! extension_loaded('gd')) {
            throw new Exception('The GD extension is not installed or enabled. Please install it to use this functionality.');
        }

        /** @var string|bool $imageFile */
        $imageFile = file_get_contents($imagePath);

        if ($imageFile === false) {
            throw new Exception('Failed to read image file');
        }

        /** @var GdImage|bool $image */
        $image = imagecreatefromstring((string) $imageFile);

        if (! $image instanceof GdImage) {
            throw new Exception('Image type is unsupported or the image is corrupt and cannot be loaded.');
        }

        /**
         * Loop through the pixels of the image; but not all of them, we loop in
         * steps of `$pixelStep` and extract image colors.
         *
         * @var array<array<int>> $imageColors
         */
        $imageColors = [];

        /** @var int|bool $imageWidth */
        $imageWidth = imagesx($image);

        /** @var int|bool $imageHeight */
        $imageHeight = imagesy($image);

        if ($imageWidth === false || $imageHeight === false) {
            throw new Exception('Failed to obtain image dimensions.');
        }

        /**
         * Calculate the pixel step required to obtain approximately the desired number of color samples
         */
        $desiredColorSamples = 5000;

        $pixelStep = sqrt(($imageWidth * $imageHeight) / $desiredColorSamples);

        // Ensure that the pixel step is at least 1
        $pixelStep = max(1, (int) $pixelStep);

        for ($x = 0; $x < $imageWidth; $x += $pixelStep) {
            for ($y = 0; $y < $imageHeight; $y += $pixelStep) {

                /** @var int|bool $rgb */
                $rgb = imagecolorat($image, $x, $y);

                if ($rgb === false) {
                    throw new Exception('Failed to retrieve color information from image.');
                }

                $imageColors[] = [
                    ($rgb >> 16) & 0xFF, // R
                    ($rgb >> 8) & 0xFF, // G
                    $rgb & 0xFF, // B
                ];
            }
        }

        return $imageColors;
    }
    // @codeCoverageIgnoreEnd
}
