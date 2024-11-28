<?php

namespace Mindee\Product\Fr\Payslip;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;

/**
 * Payslip API version 3.0 document data.
 */
class PayslipV3Document extends Prediction
{
    /**
     * @var PayslipV3BankAccountDetail Information about the employee's bank account.
     */
    public PayslipV3BankAccountDetail $bankAccountDetails;
    /**
     * @var PayslipV3Employee Information about the employee.
     */
    public PayslipV3Employee $employee;
    /**
     * @var PayslipV3Employer Information about the employer.
     */
    public PayslipV3Employer $employer;
    /**
     * @var PayslipV3Employment Information about the employment.
     */
    public PayslipV3Employment $employment;
    /**
     * @var PayslipV3PaidTimeOffs Information about paid time off.
     */
    public PayslipV3PaidTimeOffs $paidTimeOff;
    /**
     * @var PayslipV3PayDetail Detailed information about the pay.
     */
    public PayslipV3PayDetail $payDetail;
    /**
     * @var PayslipV3PayPeriod Information about the pay period.
     */
    public PayslipV3PayPeriod $payPeriod;
    /**
     * @var PayslipV3SalaryDetails Detailed information about the earnings.
     */
    public PayslipV3SalaryDetails $salaryDetails;
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
        $this->bankAccountDetails = new PayslipV3BankAccountDetail(
            $rawPrediction["bank_account_details"],
            $pageId
        );
        if (!isset($rawPrediction["employee"])) {
            throw new MindeeUnsetException();
        }
        $this->employee = new PayslipV3Employee(
            $rawPrediction["employee"],
            $pageId
        );
        if (!isset($rawPrediction["employer"])) {
            throw new MindeeUnsetException();
        }
        $this->employer = new PayslipV3Employer(
            $rawPrediction["employer"],
            $pageId
        );
        if (!isset($rawPrediction["employment"])) {
            throw new MindeeUnsetException();
        }
        $this->employment = new PayslipV3Employment(
            $rawPrediction["employment"],
            $pageId
        );
        if (!isset($rawPrediction["paid_time_off"])) {
            throw new MindeeUnsetException();
        }
        $this->paidTimeOff = new PayslipV3PaidTimeOffs(
            $rawPrediction["paid_time_off"],
            $pageId
        );
        if (!isset($rawPrediction["pay_detail"])) {
            throw new MindeeUnsetException();
        }
        $this->payDetail = new PayslipV3PayDetail(
            $rawPrediction["pay_detail"],
            $pageId
        );
        if (!isset($rawPrediction["pay_period"])) {
            throw new MindeeUnsetException();
        }
        $this->payPeriod = new PayslipV3PayPeriod(
            $rawPrediction["pay_period"],
            $pageId
        );
        if (!isset($rawPrediction["salary_details"])) {
            throw new MindeeUnsetException();
        }
        $this->salaryDetails = new PayslipV3SalaryDetails(
            $rawPrediction["salary_details"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $payPeriodToFieldList = $this->payPeriod != null ? $this->payPeriod->toFieldList() : "";
        $employeeToFieldList = $this->employee != null ? $this->employee->toFieldList() : "";
        $employerToFieldList = $this->employer != null ? $this->employer->toFieldList() : "";
        $bankAccountDetailsToFieldList = $this->bankAccountDetails != null ?
            $this->bankAccountDetails->toFieldList() : "";
        $employmentToFieldList = $this->employment != null ? $this->employment->toFieldList() : "";
        $salaryDetailsSummary = strval($this->salaryDetails);
        $payDetailToFieldList = $this->payDetail != null ? $this->payDetail->toFieldList() : "";
        $paidTimeOffSummary = strval($this->paidTimeOff);

        $outStr = ":Pay Period: $payPeriodToFieldList
:Employee: $employeeToFieldList
:Employer: $employerToFieldList
:Bank Account Details: $bankAccountDetailsToFieldList
:Employment: $employmentToFieldList
:Salary Details: $salaryDetailsSummary
:Pay Detail: $payDetailToFieldList
:Paid Time Off: $paidTimeOffSummary
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
