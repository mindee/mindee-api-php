<?php

namespace V1\Product\Fr\Payslip;

use Mindee\Parsing\Common\Document;
use Mindee\Product\Fr\Payslip;
use PHPUnit\Framework\TestCase;

class PayslipV2Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = \TestingUtilities::getV1DataDir() . "/products/payslip_fra/response_v2/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(Payslip\PayslipV2::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(Payslip\PayslipV2::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->employee->address);
        $this->assertNull($prediction->employee->dateOfBirth);
        $this->assertNull($prediction->employee->firstName);
        $this->assertNull($prediction->employee->lastName);
        $this->assertNull($prediction->employee->phoneNumber);
        $this->assertNull($prediction->employee->registrationNumber);
        $this->assertNull($prediction->employee->socialSecurityNumber);
        $this->assertNull($prediction->employer->address);
        $this->assertNull($prediction->employer->companyId);
        $this->assertNull($prediction->employer->companySite);
        $this->assertNull($prediction->employer->nafCode);
        $this->assertNull($prediction->employer->name);
        $this->assertNull($prediction->employer->phoneNumber);
        $this->assertNull($prediction->employer->urssafNumber);
        $this->assertNull($prediction->bankAccountDetails->bankName);
        $this->assertNull($prediction->bankAccountDetails->iban);
        $this->assertNull($prediction->bankAccountDetails->swift);
        $this->assertNull($prediction->employment->category);
        $this->assertNull($prediction->employment->coefficient);
        $this->assertNull($prediction->employment->collectiveAgreement);
        $this->assertNull($prediction->employment->jobTitle);
        $this->assertNull($prediction->employment->positionLevel);
        $this->assertNull($prediction->employment->startDate);
        $this->assertEquals(0, count($prediction->salaryDetails));
        $this->assertNull($prediction->payDetail->grossSalary);
        $this->assertNull($prediction->payDetail->grossSalaryYtd);
        $this->assertNull($prediction->payDetail->incomeTaxRate);
        $this->assertNull($prediction->payDetail->incomeTaxWithheld);
        $this->assertNull($prediction->payDetail->netPaid);
        $this->assertNull($prediction->payDetail->netPaidBeforeTax);
        $this->assertNull($prediction->payDetail->netTaxable);
        $this->assertNull($prediction->payDetail->netTaxableYtd);
        $this->assertNull($prediction->payDetail->totalCostEmployer);
        $this->assertNull($prediction->payDetail->totalTaxesAndDeductions);
        $this->assertNull($prediction->pto->accruedThisPeriod);
        $this->assertNull($prediction->pto->balanceEndOfPeriod);
        $this->assertNull($prediction->pto->usedThisPeriod);
        $this->assertNull($prediction->payPeriod->endDate);
        $this->assertNull($prediction->payPeriod->month);
        $this->assertNull($prediction->payPeriod->paymentDate);
        $this->assertNull($prediction->payPeriod->startDate);
        $this->assertNull($prediction->payPeriod->year);
    }
}
