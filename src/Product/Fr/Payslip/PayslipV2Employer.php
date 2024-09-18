<?php

namespace Mindee\Product\Fr\Payslip;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * Information about the employer.
 */
class PayslipV2Employer
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string|null The address of the employer.
     */
    public ?string $address;
    /**
     * @var string|null The company ID of the employer.
     */
    public ?string $companyId;
    /**
     * @var string|null The site of the company.
     */
    public ?string $companySite;
    /**
     * @var string|null The NAF code of the employer.
     */
    public ?string $nafCode;
    /**
     * @var string|null The name of the employer.
     */
    public ?string $name;
    /**
     * @var string|null The phone number of the employer.
     */
    public ?string $phoneNumber;
    /**
     * @var string|null The URSSAF number of the employer.
     */
    public ?string $urssafNumber;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->address = $rawPrediction["address"] ?? null;
        $this->companyId = $rawPrediction["company_id"] ?? null;
        $this->companySite = $rawPrediction["company_site"] ?? null;
        $this->nafCode = $rawPrediction["naf_code"] ?? null;
        $this->name = $rawPrediction["name"] ?? null;
        $this->phoneNumber = $rawPrediction["phone_number"] ?? null;
        $this->urssafNumber = $rawPrediction["urssaf_number"] ?? null;
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
        $outArr["companyId"] = SummaryHelper::formatForDisplay($this->companyId);
        $outArr["companySite"] = SummaryHelper::formatForDisplay($this->companySite);
        $outArr["nafCode"] = SummaryHelper::formatForDisplay($this->nafCode);
        $outArr["name"] = SummaryHelper::formatForDisplay($this->name);
        $outArr["phoneNumber"] = SummaryHelper::formatForDisplay($this->phoneNumber);
        $outArr["urssafNumber"] = SummaryHelper::formatForDisplay($this->urssafNumber);
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
        $outArr["companyId"] = SummaryHelper::formatForDisplay($this->companyId);
        $outArr["companySite"] = SummaryHelper::formatForDisplay($this->companySite);
        $outArr["nafCode"] = SummaryHelper::formatForDisplay($this->nafCode);
        $outArr["name"] = SummaryHelper::formatForDisplay($this->name);
        $outArr["phoneNumber"] = SummaryHelper::formatForDisplay($this->phoneNumber);
        $outArr["urssafNumber"] = SummaryHelper::formatForDisplay($this->urssafNumber);
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
        $outStr .= "\n  :Company ID: " . $printable["companyId"];
        $outStr .= "\n  :Company Site: " . $printable["companySite"];
        $outStr .= "\n  :NAF Code: " . $printable["nafCode"];
        $outStr .= "\n  :Name: " . $printable["name"];
        $outStr .= "\n  :Phone Number: " . $printable["phoneNumber"];
        $outStr .= "\n  :URSSAF Number: " . $printable["urssafNumber"];
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
