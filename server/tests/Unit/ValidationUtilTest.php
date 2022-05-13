<?php

namespace Tests\Unit;

use App\Enums\PlanEnum;
use App\Utils\ValidationUtils;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testValidateAlphaNumSpaceInvalidValue()
    {
        $this->assertFalse(ValidationUtils::isAlphaNumericSpaceOnly("hello world!"));
        $this->assertFalse(ValidationUtils::isAlphaNumericSpaceOnly("hello world*"));
        $this->assertFalse(ValidationUtils::isAlphaNumericSpaceOnly("hello world("));
        $this->assertFalse(ValidationUtils::isAlphaNumericSpaceOnly("hello world+"));
        $this->assertFalse(ValidationUtils::isAlphaNumericSpaceOnly("hello world^"));
        $this->assertFalse(ValidationUtils::isAlphaNumericSpaceOnly("hello world@"));
        $this->assertFalse(ValidationUtils::isAlphaNumericSpaceOnly("hello world`"));
    }

    public function testValidateAlphaNumSpaceValidValue()
    {
        $this->assertTrue(ValidationUtils::isAlphaNumericSpaceOnly("hello world"));
        $this->assertTrue(ValidationUtils::isAlphaNumericSpaceOnly("Benjamin was HERE"));
    }

    public function testValidatePlanEnumInvalidValue()
    {
        $this->assertFalse(ValidationUtils::isPlanEnum("OTHER_ENUM"));
        $this->assertFalse(ValidationUtils::isPlanEnum("TEST_ENUM"));
    }

    public function testValidatePlanEnumValidValue()
    {
        $this->assertTrue(ValidationUtils::isPlanEnum(PlanEnum::FlightPlan));
        $this->assertTrue(ValidationUtils::isPlanEnum(PlanEnum::ActivityPlan));
        $this->assertTrue(ValidationUtils::isPlanEnum(PlanEnum::LodgingPlan));
        $this->assertTrue(ValidationUtils::isPlanEnum(PlanEnum::TransportPlan));
    }
}
