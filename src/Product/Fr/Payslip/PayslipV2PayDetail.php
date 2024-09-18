<?php

namespace Mindee\Product\Fr\Payslip;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * Detailed information about the pay.
 */
class PayslipV2PayDetail
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

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
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->grossSalary = isset($rawPrediction["gross_salary"]) ?
            floatval($rawPrediction["gross_salary"]) : null;
        $this->grossSalaryYtd = isset($rawPrediction["gross_salary_ytd"]) ?
            floatval($rawPrediction["gross_salary_ytd"]) : null;
        $this->incomeTaxRate = isset($rawPrediction["income_tax_rate"]) ?
            floatval($rawPrediction["income_tax_rate"]) : null;
        $this->incomeTaxWithheld = isset($rawPrediction["income_tax_withheld"]) ?
            floatval($rawPrediction["income_tax_withheld"]) : null;
        $this->netPaid = isset($rawPrediction["net_paid"]) ?
            floatval($rawPrediction["net_paid"]) : null;
        $this->netPaidBeforeTax = isset($rawPrediction["net_paid_before_tax"]) ?
            floatval($rawPrediction["net_paid_before_tax"]) : null;
        $this->netTaxable = isset($rawPrediction["net_taxable"]) ?
            floatval($rawPrediction["net_taxable"]) : null;
        $this->netTaxableYtd = isset($rawPrediction["net_taxable_ytd"]) ?
            floatval($rawPrediction["net_taxable_ytd"]) : null;
        $this->totalCostEmployer = isset($rawPrediction["total_cost_employer"]) ?
            floatval($rawPrediction["total_cost_employer"]) : null;
        $this->totalTaxesAndDeductions = isset($rawPrediction["total_taxes_and_deductions"]) ?
            floatval($rawPrediction["total_taxes_and_deductions"]) : null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["grossSalary"] = SummaryHelper::formatFloat($this->grossSalary);
        $outArr["grossSalaryYtd"] = SummaryHelper::formatFloat($this->grossSalaryYtd);
        $outArr["incomeTaxRate"] = SummaryHelper::formatFloat($this->incomeTaxRate);
        $outArr["incomeTaxWithheld"] = SummaryHelper::formatFloat($this->incomeTaxWithheld);
        $outArr["netPaid"] = SummaryHelper::formatFloat($this->netPaid);
        $outArr["netPaidBeforeTax"] = SummaryHelper::formatFloat($this->netPaidBeforeTax);
        $outArr["netTaxable"] = SummaryHelper::formatFloat($this->netTaxable);
        $outArr["netTaxableYtd"] = SummaryHelper::formatFloat($this->netTaxableYtd);
        $outArr["totalCostEmployer"] = SummaryHelper::formatFloat($this->totalCostEmployer);
        $outArr["totalTaxesAndDeductions"] = SummaryHelper::formatFloat($this->totalTaxesAndDeductions);
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
        $outArr["grossSalary"] = SummaryHelper::formatFloat($this->grossSalary);
        $outArr["grossSalaryYtd"] = SummaryHelper::formatFloat($this->grossSalaryYtd);
        $outArr["incomeTaxRate"] = SummaryHelper::formatFloat($this->incomeTaxRate);
        $outArr["incomeTaxWithheld"] = SummaryHelper::formatFloat($this->incomeTaxWithheld);
        $outArr["netPaid"] = SummaryHelper::formatFloat($this->netPaid);
        $outArr["netPaidBeforeTax"] = SummaryHelper::formatFloat($this->netPaidBeforeTax);
        $outArr["netTaxable"] = SummaryHelper::formatFloat($this->netTaxable);
        $outArr["netTaxableYtd"] = SummaryHelper::formatFloat($this->netTaxableYtd);
        $outArr["totalCostEmployer"] = SummaryHelper::formatFloat($this->totalCostEmployer);
        $outArr["totalTaxesAndDeductions"] = SummaryHelper::formatFloat($this->totalTaxesAndDeductions);
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
        return SummaryHelper::cleanOutString($this->toFieldList());
    }
}
