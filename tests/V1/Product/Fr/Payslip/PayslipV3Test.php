<?php

declare(strict_types=1);

namespace V1\Product\Fr\Payslip;

use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\Fr\Payslip\PayslipV3;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class PayslipV3Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/payslip_fra/response_v3/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(PayslipV3::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(PayslipV3::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->payPeriod->endDate);
        self::assertNull($prediction->payPeriod->month);
        self::assertNull($prediction->payPeriod->paymentDate);
        self::assertNull($prediction->payPeriod->startDate);
        self::assertNull($prediction->payPeriod->year);
        self::assertNull($prediction->employee->address);
        self::assertNull($prediction->employee->dateOfBirth);
        self::assertNull($prediction->employee->firstName);
        self::assertNull($prediction->employee->lastName);
        self::assertNull($prediction->employee->phoneNumber);
        self::assertNull($prediction->employee->registrationNumber);
        self::assertNull($prediction->employee->socialSecurityNumber);
        self::assertNull($prediction->employer->address);
        self::assertNull($prediction->employer->companyId);
        self::assertNull($prediction->employer->companySite);
        self::assertNull($prediction->employer->nafCode);
        self::assertNull($prediction->employer->name);
        self::assertNull($prediction->employer->phoneNumber);
        self::assertNull($prediction->employer->urssafNumber);
        self::assertNull($prediction->bankAccountDetails->bankName);
        self::assertNull($prediction->bankAccountDetails->iban);
        self::assertNull($prediction->bankAccountDetails->swift);
        self::assertNull($prediction->employment->category);
        self::assertNull($prediction->employment->coefficient);
        self::assertNull($prediction->employment->collectiveAgreement);
        self::assertNull($prediction->employment->jobTitle);
        self::assertNull($prediction->employment->positionLevel);
        self::assertNull($prediction->employment->seniorityDate);
        self::assertNull($prediction->employment->startDate);
        self::assertCount(0, $prediction->salaryDetails);
        self::assertNull($prediction->payDetail->grossSalary);
        self::assertNull($prediction->payDetail->grossSalaryYtd);
        self::assertNull($prediction->payDetail->incomeTaxRate);
        self::assertNull($prediction->payDetail->incomeTaxWithheld);
        self::assertNull($prediction->payDetail->netPaid);
        self::assertNull($prediction->payDetail->netPaidBeforeTax);
        self::assertNull($prediction->payDetail->netTaxable);
        self::assertNull($prediction->payDetail->netTaxableYtd);
        self::assertNull($prediction->payDetail->totalCostEmployer);
        self::assertNull($prediction->payDetail->totalTaxesAndDeductions);
        self::assertCount(0, $prediction->paidTimeOff);
    }
}
