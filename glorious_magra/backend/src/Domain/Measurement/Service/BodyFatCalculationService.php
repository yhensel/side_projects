<?php

declare(strict_types=1);

namespace App\Domain\Measurement\Service;

use App\Domain\User\ValueObject\BiologicalSex;

class BodyFatCalculationService
{
    private const float CENTIMETERS_PER_INCH = 2.54;

    public function calculate(
        BiologicalSex $biologicalSex,
        float $height,
        float $neck,
        float $waist,
        float $hip,
    ): float {
        $measurementsToValidate = match ($biologicalSex) {
            BiologicalSex::MALE => [$height, $neck, $waist],
            BiologicalSex::FEMALE => [$height, $neck, $waist, $hip],
        };

        $this->assertPositiveMeasurements(...$measurementsToValidate);

        $heightInInches = $this->centimetersToInches($height);
        $neckInInches = $this->centimetersToInches($neck);
        $waistInInches = $this->centimetersToInches($waist);
        $hipInInches = $this->centimetersToInches($hip);

        $percentage = match ($biologicalSex) {
            BiologicalSex::MALE => $this->calculateMale($heightInInches, $neckInInches, $waistInInches),
            BiologicalSex::FEMALE => $this->calculateFemale($heightInInches, $neckInInches, $waistInInches, $hipInInches),
        };

        return round($percentage, 2);
    }

    private function calculateMale(float $height, float $neck, float $waist): float
    {
        $circumferenceDifference = $waist - $neck;

        if ($circumferenceDifference <= 0) {
            throw new \InvalidArgumentException('Waist must be greater than neck for male body fat calculation.');
        }

        return 495 / (1.0324 - 0.19077 * log10($circumferenceDifference) + 0.15456 * log10($height)) - 450;
    }

    private function calculateFemale(float $height, float $neck, float $waist, float $hip): float
    {
        $circumferenceSum = $waist + $hip - $neck;

        if ($circumferenceSum <= 0) {
            throw new \InvalidArgumentException('Waist plus hip must be greater than neck for female body fat calculation.');
        }

        return 495 / (1.29579 - 0.35004 * log10($circumferenceSum) + 0.22100 * log10($height)) - 450;
    }

    private function centimetersToInches(float $value): float
    {
        return $value / self::CENTIMETERS_PER_INCH;
    }

    private function assertPositiveMeasurements(float ...$measurements): void
    {
        foreach ($measurements as $measurement) {
            if ($measurement <= 0) {
                throw new \InvalidArgumentException('Body measurements must be positive numbers.');
            }
        }
    }
}
