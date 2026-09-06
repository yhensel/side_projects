<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Measurement\Service;

use App\Domain\Measurement\Service\BodyFatCalculationService;
use App\Domain\User\ValueObject\BiologicalSex;
use PHPUnit\Framework\TestCase;

class BodyFatCalculationServiceTest extends TestCase
{
    public function testItCalculatesMaleBodyFatPercentage(): void
    {
        $service = new BodyFatCalculationService();

        $result = $service->calculate(
            biologicalSex: BiologicalSex::MALE,
            height: 180.0,
            neck: 40.0,
            waist: 86.0,
            hip: 95.0,
        );

        $this->assertSame(9.0, $result);
    }

    public function testItCalculatesFemaleBodyFatPercentage(): void
    {
        $service = new BodyFatCalculationService();

        $result = $service->calculate(
            biologicalSex: BiologicalSex::FEMALE,
            height: 165.0,
            neck: 32.0,
            waist: 72.0,
            hip: 98.0,
        );

        $this->assertSame(4.52, $result);
    }

    public function testItRejectsImpossibleMaleCircumferences(): void
    {
        $service = new BodyFatCalculationService();

        $this->expectException(\InvalidArgumentException::class);

        $service->calculate(
            biologicalSex: BiologicalSex::MALE,
            height: 180.0,
            neck: 90.0,
            waist: 80.0,
            hip: 95.0,
        );
    }
}
