<?php

namespace Mindee\Product\Fr\Payslip;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;

/**
 * Payslip API version 2.0 document data.
 */
class PayslipV2Document extends Prediction
{
    /**
     * @var PayslipV2BankAccountDetail Information about the employee's bank account.
     */
    public PayslipV2BankAccountDetail $bankAccountDetails;
    /**
     * @var PayslipV2Employee Information about the employee.
     */
    public PayslipV2Employee $employee;
    /**
     * @var PayslipV2Employer Information about the employer.
     */
    public PayslipV2Employer $employer;
    /**
     * @var PayslipV2Employment Information about the employment.
     */
    public PayslipV2Employment $employment;
    /**
     * @var PayslipV2PayDetail Detailed information about the pay.
     */
    public PayslipV2PayDetail $payDetail;
    /**
     * @var PayslipV2PayPeriod Information about the pay period.
     */
    public PayslipV2PayPeriod $payPeriod;
    /**
     * @var PayslipV2Pto Information about paid time off.
     */
    public PayslipV2Pto $pto;
    /**
     * @var PayslipV2SalaryDetails Detailed information about the earnings.
     */
    public PayslipV2SalaryDetails $salaryDetails;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["bank_account_details"])) {
            throw new MindeeUnsetException();
        }
        $this->bankAccountDetails = new PayslipV2BankAccountDetail(
            $rawPrediction["bank_account_details"],
            $pageId
        );
        if (!isset($rawPrediction["employee"])) {
            throw new MindeeUnsetException();
        }
        $this->employee = new PayslipV2Employee(
            $rawPrediction["employee"],
            $pageId
        );
        if (!isset($rawPrediction["employer"])) {
            throw new MindeeUnsetException();
        }
        $this->employer = new PayslipV2Employer(
            $rawPrediction["employer"],
            $pageId
        );
        if (!isset($rawPrediction["employment"])) {
            throw new MindeeUnsetException();
        }
        $this->employment = new PayslipV2Employment(
            $rawPrediction["employment"],
            $pageId
        );
        if (!isset($rawPrediction["pay_detail"])) {
            throw new MindeeUnsetException();
        }
        $this->payDetail = new PayslipV2PayDetail(
            $rawPrediction["pay_detail"],
            $pageId
        );
        if (!isset($rawPrediction["pay_period"])) {
            throw new MindeeUnsetException();
        }
        $this->payPeriod = new PayslipV2PayPeriod(
            $rawPrediction["pay_period"],
            $pageId
        );
        if (!isset($rawPrediction["pto"])) {
            throw new MindeeUnsetException();
        }
        $this->pto = new PayslipV2Pto(
            $rawPrediction["pto"],
            $pageId
        );
        if (!isset($rawPrediction["salary_details"])) {
            throw new MindeeUnsetException();
        }
        $this->salaryDetails = new PayslipV2SalaryDetails(
            $rawPrediction["salary_details"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $employeeToFieldList = $this->employee != null ? $this->employee->toFieldList() : "";
        $employerToFieldList = $this->employer != null ? $this->employer->toFieldList() : "";
        $bankAccountDetailsToFieldList = $this->bankAccountDetails != null ?
            $this->bankAccountDetails->toFieldList() : "";
        $employmentToFieldList = $this->employment != null ? $this->employment->toFieldList() : "";
        $salaryDetailsSummary = strval($this->salaryDetails);
        $payDetailToFieldList = $this->payDetail != null ? $this->payDetail->toFieldList() : "";
        $ptoToFieldList = $this->pto != null ? $this->pto->toFieldList() : "";
        $payPeriodToFieldList = $this->payPeriod != null ? $this->payPeriod->toFieldList() : "";

        $outStr = ":Employee: $employeeToFieldList
:Employer: $employerToFieldList
:Bank Account Details: $bankAccountDetailsToFieldList
:Employment: $employmentToFieldList
:Salary Details: $salaryDetailsSummary
:Pay Detail: $payDetailToFieldList
:PTO: $ptoToFieldList
:Pay Period: $payPeriodToFieldList
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
