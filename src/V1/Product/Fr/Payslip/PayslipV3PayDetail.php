<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Fr\Payslip;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * Detailed information about the pay.
 */
class PayslipV3PayDetail
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var float|null The gross salary of the employee.
     */
    public ?float $grossSalary;
    /**
     * @var float|null The year-to-date gross salary of the employee.
     */
    public ?float $grossSalaryYtd;
    /**
     * @var float|null The income tax rate of the employee.
     */
    public ?float $incomeTaxRate;
    /**
     * @var float|null The income tax withheld from the employee's pay.
     */
    public ?float $incomeTaxWithheld;
    /**
     * @var float|null The net paid amount of the employee.
     */
    public ?float $netPaid;
    /**
     * @var float|null The net paid amount before tax of the employee.
     */
    public ?float $netPaidBeforeTax;
    /**
     * @var float|null The net taxable amount of the employee.
     */
    public ?float $netTaxable;
    /**
     * @var float|null The year-to-date net taxable amount of the employee.
     */
    public ?float $netTaxableYtd;
    /**
     * @var float|null The total cost to the employer.
     */
    public ?float $totalCostEmployer;
    /**
     * @var float|null The total taxes and deductions of the employee.
     */
    public ?float $totalTaxesAndDeductions;
    /**
     * @var integer|null Page ID.
     */
    public ?int $pageId;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->grossSalary = isset($rawPrediction["gross_salary"])
            ? (float) ($rawPrediction["gross_salary"]) : null;
        $this->grossSalaryYtd = isset($rawPrediction["gross_salary_ytd"])
            ? (float) ($rawPrediction["gross_salary_ytd"]) : null;
        $this->incomeTaxRate = isset($rawPrediction["income_tax_rate"])
            ? (float) ($rawPrediction["income_tax_rate"]) : null;
        $this->incomeTaxWithheld = isset($rawPrediction["income_tax_withheld"])
            ? (float) ($rawPrediction["income_tax_withheld"]) : null;
        $this->netPaid = isset($rawPrediction["net_paid"])
            ? (float) ($rawPrediction["net_paid"]) : null;
        $this->netPaidBeforeTax = isset($rawPrediction["net_paid_before_tax"])
            ? (float) ($rawPrediction["net_paid_before_tax"]) : null;
        $this->netTaxable = isset($rawPrediction["net_taxable"])
            ? (float) ($rawPrediction["net_taxable"]) : null;
        $this->netTaxableYtd = isset($rawPrediction["net_taxable_ytd"])
            ? (float) ($rawPrediction["net_taxable_ytd"]) : null;
        $this->totalCostEmployer = isset($rawPrediction["total_cost_employer"])
            ? (float) ($rawPrediction["total_cost_employer"]) : null;
        $this->totalTaxesAndDeductions = isset($rawPrediction["total_taxes_and_deductions"])
            ? (float) ($rawPrediction["total_taxes_and_deductions"]) : null;
        $this->pageId = $pageId;
    }

    /**
     * Return values for printing as an array.
     * @return array<string, string>
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["grossSalary"] = SummaryHelperV1::formatFloat($this->grossSalary);
        $outArr["grossSalaryYtd"] = SummaryHelperV1::formatFloat($this->grossSalaryYtd);
        $outArr["incomeTaxRate"] = SummaryHelperV1::formatFloat($this->incomeTaxRate);
        $outArr["incomeTaxWithheld"] = SummaryHelperV1::formatFloat($this->incomeTaxWithheld);
        $outArr["netPaid"] = SummaryHelperV1::formatFloat($this->netPaid);
        $outArr["netPaidBeforeTax"] = SummaryHelperV1::formatFloat($this->netPaidBeforeTax);
        $outArr["netTaxable"] = SummaryHelperV1::formatFloat($this->netTaxable);
        $outArr["netTaxableYtd"] = SummaryHelperV1::formatFloat($this->netTaxableYtd);
        $outArr["totalCostEmployer"] = SummaryHelperV1::formatFloat($this->totalCostEmployer);
        $outArr["totalTaxesAndDeductions"] = SummaryHelperV1::formatFloat($this->totalTaxesAndDeductions);
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
        $outStr .= "\n  :Gross Salary: " . $printable["grossSalary"];
        $outStr .= "\n  :Gross Salary YTD: " . $printable["grossSalaryYtd"];
        $outStr .= "\n  :Income Tax Rate: " . $printable["incomeTaxRate"];
        $outStr .= "\n  :Income Tax Withheld: " . $printable["incomeTaxWithheld"];
        $outStr .= "\n  :Net Paid: " . $printable["netPaid"];
        $outStr .= "\n  :Net Paid Before Tax: " . $printable["netPaidBeforeTax"];
        $outStr .= "\n  :Net Taxable: " . $printable["netTaxable"];
        $outStr .= "\n  :Net Taxable YTD: " . $printable["netTaxableYtd"];
        $outStr .= "\n  :Total Cost Employer: " . $printable["totalCostEmployer"];
        $outStr .= "\n  :Total Taxes and Deductions: " . $printable["totalTaxesAndDeductions"];
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
