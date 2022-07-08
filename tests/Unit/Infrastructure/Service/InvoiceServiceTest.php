<?php

declare(strict_types=1);

namespace Billie\Tests\Unit\Infrastructure\Service;

use Billie\DataLayer\Entity\Company;
use Billie\DataLayer\Entity\Invoice;
use Billie\DataLayer\Enums\InvoiceStatus;
use Billie\DataLayer\Repository\CompanyRepositoryInterface;
use Billie\DataLayer\Repository\InvoiceRepositoryInterface;
use Billie\Infrastructure\Dto\V1\Invoice as InvoiceDtoV1;
use Billie\Infrastructure\Exception\DebtorLimitExceededException;
use Billie\Infrastructure\Exception\InvoiceCreateValidationException;
use Billie\Infrastructure\Service\CompanyServiceInterface;
use Billie\Infrastructure\Service\InvoiceService;
use Billie\Infrastructure\Service\InvoiceServiceInterface;
use Billie\Tests\TestTrait\TestCompanyTrait;
use Billie\Tests\TestTrait\TestInvoiceTrait;
use Billie\Tests\TestTrait\TestObjectTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InvoiceServiceTest extends TestCase
{
    use TestCompanyTrait;
    use TestInvoiceTrait;
    use TestObjectTrait;

    public function testCreateFromJsonFailOnValidation(): void
    {
        // Arrange
        //
        $json = '{}';

        $invoiceRepositoryMock = $this->getMockBuilder(InvoiceRepositoryInterface::class)->getMock();
        $invoiceRepositoryMock
            ->expects($this->never())
            ->method('add')
            ->with($this->isInstanceOf(Invoice::class), true);

        $serializerMock = $this->getMockBuilder(SerializerInterface::class)->getMock();
        $testCompanyDto = $this->createTestInvoiceDto();
        $serializerMock
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($testCompanyDto);

        $validatorMock = $this->getMockBuilder(ValidatorInterface::class)->getMock();
        $validatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList([
                new ConstraintViolation('Invalid field', null, [], 0, null, null),
            ]));

        $service = $this->getInvoiceService(
            $invoiceRepositoryMock,
            null,
            $serializerMock,
            $validatorMock
        );

        $this->expectException(InvoiceCreateValidationException::class);
        $this->expectExceptionMessage('Invoice entry cannot created due to validation errors: Invalid field');

        // Act
        $service->createFromJson($json);
    }

    public function testCreateFromJsonSuccess(): void
    {
        // Arrange
        //
        $json = '{}';

        $invoiceRepositoryMock = $this->getMockBuilder(InvoiceRepositoryInterface::class)->getMock();
        $invoiceRepositoryMock
            //->expects($this->exactly(2)) // two times, in fact + 1 inside mocked "addInvoice()"
            ->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf(Invoice::class), true);

        $serializerMock = $this->getMockBuilder(SerializerInterface::class)->getMock();
        $testCompanyDto = $this->createTestInvoiceDto();
        $serializerMock
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($testCompanyDto);

        $validatorMock = $this->getMockBuilder(ValidatorInterface::class)->getMock();
        $validatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $service = $this->getInvoiceServicePartialMock(
            $invoiceRepositoryMock,
            null,
            $serializerMock,
            $validatorMock,
            ['verifyOpenInvoiceLimitExceeded', 'addInvoice'],
        );
        $testInvoice = $this->setObjectPropertyValue($this->createTestInvoice(), 'id', Uuid::v4());
        $service
            ->expects($this->once())
            ->method('addInvoice')
            ->willReturn($testInvoice);

        $service
            ->expects($this->once())
            ->method('verifyOpenInvoiceLimitExceeded')
            ->with($testInvoice, true)
            ->willReturn(true);

        // Act
        $newInvoice = $service->createFromJson($json);

        // Assert
        $this->assertInstanceOf(InvoiceDtoV1::class, $newInvoice);
        $this->assertSame(InvoiceStatus::OPEN->value, $testCompanyDto->getStatus());
    }

    /**
     * @testWith [ 10, 100, 110, false ]
     *           [ 10, 99, 110, false ]
     *           [ 10, 110, 110, true ]
     *           [ 10, 110, 110, true, true]
     */
    public function testVerifyOpenInvoiceLimitExceeded(
        int $invoiceAmount,
        int $openAmount,
        int $limit,
        bool $expected,
        bool $shouldThrow = false,
    ): void {
        $testInvoice = $this->createTestInvoice();
        $testInvoice->setTotal($invoiceAmount);
        $testInvoice->getDebtor()->setDebtorLimit($limit);

        $this->setObjectPropertyValue($testInvoice, 'id', Uuid::v4());
        $this->setObjectPropertyValue($testInvoice->getDebtor(), 'id', Uuid::v4());

        $companyServiceMock = $this->getMockBuilder(CompanyServiceInterface::class)->getMock();
        $companyServiceMock
            ->expects($this->once())
            ->method('getTotalOpenInvoicesAmount')
            ->willReturn($openAmount);

        $invoiceRepositoryMock = $this->getMockBuilder(InvoiceRepositoryInterface::class)->getMock();
        $invoiceRepositoryMock
            ->expects($expected === true ? $this->once() : $this->never())
            ->method('add')
            ->with($this->isInstanceOf(Invoice::class));

        $service = $this->getInvoiceService($invoiceRepositoryMock, $companyServiceMock);

        if ($shouldThrow) {
            $this->expectException(DebtorLimitExceededException::class);
            $this->expectExceptionMessage(sprintf(
                'Invoice rejected. Reason: Invoice "%s" for "%s" exceeds limit by 10 Eur',
                $testInvoice->getIdAsString(),
                $testInvoice->getDebtor()->getIdAsString(),
            ));
        }

        $this->assertSame($expected, $service->verifyOpenInvoiceLimitExceeded($testInvoice, $shouldThrow));
    }

    /**
     * @param array<int, string> $mockedMethods
     * @return MockObject&InvoiceService
     */
    private function getInvoiceServicePartialMock(
        InvoiceRepositoryInterface $invoiceRepositoryMock = null,
        CompanyServiceInterface $companyServiceMock = null,
        SerializerInterface $serializerMock = null,
        ValidatorInterface $validatorMock = null,
        array $mockedMethods = [],
    ) {
        return $this->getMockBuilder(InvoiceService::class)
            ->setConstructorArgs([
                $invoiceRepositoryMock ?: $this->createMock(InvoiceRepositoryInterface::class),
                $companyServiceMock ?: $this->createMock(CompanyServiceInterface::class),
                $serializerMock ?: $this->createMock(SerializerInterface::class),
                $validatorMock ?: $this->createMock(ValidatorInterface::class),
            ])
            ->onlyMethods($mockedMethods)
            ->getMock();
    }

    private function getInvoiceService(
        InvoiceRepositoryInterface $invoiceRepositoryMock = null,
        CompanyServiceInterface $companyServiceMock = null,
        SerializerInterface $serializerMock = null,
        ValidatorInterface $validatorMock = null,
    ): InvoiceServiceInterface {
        return new InvoiceService(
            $invoiceRepositoryMock ?: $this->createMock(InvoiceRepositoryInterface::class),
            $companyServiceMock ?: $this->createMock(CompanyServiceInterface::class),
            $serializerMock ?: $this->createMock(SerializerInterface::class),
            $validatorMock ?: $this->createMock(ValidatorInterface::class),
        );
    }
}
