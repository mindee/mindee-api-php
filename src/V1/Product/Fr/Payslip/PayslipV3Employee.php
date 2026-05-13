<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Fr\Payslip;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * Information about the employee.
 */
class PayslipV3Employee
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var string|null The address of the employee.
     */
    public ?string $address;
    /**
     * @var string|null The date of birth of the employee.
     */
    public ?string $dateOfBirth;
    /**
     * @var string|null The first name of the employee.
     */
    public ?string $firstName;
    /**
     * @var string|null The last name of the employee.
     */
    public ?string $lastName;
    /**
     * @var string|null The phone number of the employee.
     */
    public ?string $phoneNumber;
    /**
     * @var string|null The registration number of the employee.
     */
    public ?string $registrationNumber;
    /**
     * @var string|null The social security number of the employee.
     */
    public ?string $socialSecurityNumber;
    /**
     * @var integer|null Page ID.
     */
    public ?int $pageId;

    /**
     * @param array<string, mixed> $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->address = $rawPrediction["address"] ?? null;
        $this->dateOfBirth = $rawPrediction["date_of_birth"] ?? null;
        $this->firstName = $rawPrediction["first_name"] ?? null;
        $this->lastName = $rawPrediction["last_name"] ?? null;
        $this->pageId = $pageId;
        $this->phoneNumber = $rawPrediction["phone_number"] ?? null;
        $this->registrationNumber = $rawPrediction["registration_number"] ?? null;
        $this->socialSecurityNumber = $rawPrediction["social_security_number"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["address"] = SummaryHelperV1::formatForDisplay($this->address);
        $outArr["dateOfBirth"] = SummaryHelperV1::formatForDisplay($this->dateOfBirth);
        $outArr["firstName"] = SummaryHelperV1::formatForDisplay($this->firstName);
        $outArr["lastName"] = SummaryHelperV1::formatForDisplay($this->lastName);
        $outArr["phoneNumber"] = SummaryHelperV1::formatForDisplay($this->phoneNumber);
        $outArr["registrationNumber"] = SummaryHelperV1::formatForDisplay($this->registrationNumber);
        $outArr["socialSecurityNumber"] = SummaryHelperV1::formatForDisplay($this->socialSecurityNumber);
        return $outArr;
    }

    /**
     * Return values for printing as an array.
     * @return array<string, string>
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["address"] = SummaryHelperV1::formatForDisplay($this->address);
        $outArr["dateOfBirth"] = SummaryHelperV1::formatForDisplay($this->dateOfBirth);
        $outArr["firstName"] = SummaryHelperV1::formatForDisplay($this->firstName);
        $outArr["lastName"] = SummaryHelperV1::formatForDisplay($this->lastName);
        $outArr["phoneNumber"] = SummaryHelperV1::formatForDisplay($this->phoneNumber);
        $outArr["registrationNumber"] = SummaryHelperV1::formatForDisplay($this->registrationNumber);
        $outArr["socialSecurityNumber"] = SummaryHelperV1::formatForDisplay($this->socialSecurityNumber);
        return $outArr;
    }
    /**
     * Output in a format suitable for inclusion in a field list.
     *
     */
    public function toFieldList(): string
    {
        $printable = $this->printableValues();
        $outStr = "";
        $outStr .= "\n  :Address: " . $printable["address"];
        $outStr .= "\n  :Date of Birth: " . $printable["dateOfBirth"];
        $outStr .= "\n  :First Name: " . $printable["firstName"];
        $outStr .= "\n  :Last Name: " . $printable["lastName"];
        $outStr .= "\n  :Phone Number: " . $printable["phoneNumber"];
        $outStr .= "\n  :Registration Number: " . $printable["registrationNumber"];
        $outStr .= "\n  :Social Security Number: " . $printable["socialSecurityNumber"];
        return rtrim($outStr);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return SummaryHelperV1::cleanOutString($this->toFieldList());
    }
}
