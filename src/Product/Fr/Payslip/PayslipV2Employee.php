<?php

namespace Mindee\Product\Fr\Payslip;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * Information about the employee.
 */
class PayslipV2Employee
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

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
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->address = $rawPrediction["address"] ?? null;
        $this->dateOfBirth = $rawPrediction["date_of_birth"] ?? null;
        $this->firstName = $rawPrediction["first_name"] ?? null;
        $this->lastName = $rawPrediction["last_name"] ?? null;
        $this->phoneNumber = $rawPrediction["phone_number"] ?? null;
        $this->registrationNumber = $rawPrediction["registration_number"] ?? null;
        $this->socialSecurityNumber = $rawPrediction["social_security_number"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["address"] = SummaryHelper::formatForDisplay($this->address);
        $outArr["dateOfBirth"] = SummaryHelper::formatForDisplay($this->dateOfBirth);
        $outArr["firstName"] = SummaryHelper::formatForDisplay($this->firstName);
        $outArr["lastName"] = SummaryHelper::formatForDisplay($this->lastName);
        $outArr["phoneNumber"] = SummaryHelper::formatForDisplay($this->phoneNumber);
        $outArr["registrationNumber"] = SummaryHelper::formatForDisplay($this->registrationNumber);
        $outArr["socialSecurityNumber"] = SummaryHelper::formatForDisplay($this->socialSecurityNumber);
        return $outArr;
    }

    /**
     * Return values for printing as an array.
     *
     * @return array
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["address"] = SummaryHelper::formatForDisplay($this->address);
        $outArr["dateOfBirth"] = SummaryHelper::formatForDisplay($this->dateOfBirth);
        $outArr["firstName"] = SummaryHelper::formatForDisplay($this->firstName);
        $outArr["lastName"] = SummaryHelper::formatForDisplay($this->lastName);
        $outArr["phoneNumber"] = SummaryHelper::formatForDisplay($this->phoneNumber);
        $outArr["registrationNumber"] = SummaryHelper::formatForDisplay($this->registrationNumber);
        $outArr["socialSecurityNumber"] = SummaryHelper::formatForDisplay($this->socialSecurityNumber);
        return $outArr;
    }
    /**
     * Output in a format suitable for inclusion in a field list.
     *
     * @return string
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
        return SummaryHelper::cleanOutString($this->toFieldList());
    }
}
