<?php

declare(strict_types=1);

namespace Billie\Tests\Unit\Infrastructure\Service;

use Billie\DataLayer\Entity\Company;
use Billie\DataLayer\Repository\CompanyRepositoryInterface;
use Billie\DataLayer\Repository\InvoiceRepositoryInterface;
use Billie\Infrastructure\Dto\V1\Company as CompanyDtoV1;
use Billie\Infrastructure\Exception\CompanyCreateValidationException;
use Billie\Infrastructure\Exception\CompanyNotFoundException;
use Billie\Infrastructure\Repository\CompanyRepository;
use Billie\Infrastructure\Service\CompanyService;
use Billie\Tests\TestTrait\TestCompanyTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CompanyServiceTest extends TestCase
{
    use TestCompanyTrait;

    public function testAddCompanySuccess(): void
    {
        $testCompany = $this->createTestCompanyDto('testName');

        $repositoryMock = $this->getMockBuilder(CompanyRepositoryInterface::class)->getMock();
        $repositoryMock
            ->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf(Company::class), true);

        $service = $this->getCompanyService($repositoryMock);

        $newEntry = $service->addCompany($testCompany);
        $this->assertInstanceOf(Company::class, $newEntry);
        $this->assertSame('testName', $newEntry->getName());
    }

    public function testGetCompanyByNameSuccess(): void
    {
        $testCompanyName = 'randomName';

        $repositoryMock = $this->getMockBuilder(CompanyRepositoryInterface::class)->getMock();
        $repositoryMock
            ->expects($this->once())
            ->method('findOneByName')
            ->with($testCompanyName)
            ->willReturn(new Company())
        ;

        $service = $this->getCompanyService($repositoryMock);

        $newEntry = $service->getCompanyByName($testCompanyName);
        $this->assertInstanceOf(Company::class, $newEntry);
    }

    public function testGetCompanyByNameThrowsException(): void
    {
        $testCompanyName = 'randomName';

        $repositoryMock = $this->getMockBuilder(CompanyRepositoryInterface::class)->getMock();
        $repositoryMock
            ->expects($this->once())
            ->method('findOneByName')
            ->with($testCompanyName)
            ->willReturn(null)
        ;

        $service = $this->getCompanyService($repositoryMock);

        $this->expectException(CompanyNotFoundException::class);
        $service->getCompanyByName($testCompanyName);
    }

    public function testGetCompanyByIdSuccess(): void
    {
        $testCompanyId = Uuid::v4();

        $repositoryMock = $this->getMockBuilder(CompanyRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $testCompanyId])
            ->willReturn(new Company());

        $service = $this->getCompanyService($repositoryMock);

        $newEntry = $service->getCompanyById($testCompanyId);
        $this->assertInstanceOf(Company::class, $newEntry);
    }

    public function testGetCompanyByIdUnknownCompanyThrowsException(): void
    {
        $testCompanyId = Uuid::v4();

        $repositoryMock = $this->getMockBuilder(CompanyRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $testCompanyId])
            ->willReturn(null);

        $service = $this->getCompanyService($repositoryMock);

        $this->expectException(CompanyNotFoundException::class);
        $this->expectExceptionMessageMatches('/^Company .+ not found. See logs$/');
        $service->getCompanyById($testCompanyId);
    }

    public function testAddCompanyFromJsonWrongStatusFails(): void
    {
        $json = file_get_contents(sprintf('/%s/new-company-data-wrong-status-code.json', TESTS_FIXTURES_PATH));
        assert(is_string($json));

        $repositoryMock = $this->getMockBuilder(CompanyRepositoryInterface::class)->getMock();
        $repositoryMock
            ->expects($this->never())
            ->method('add');

        $serializerMock = $this->getMockBuilder(SerializerInterface::class)->getMock();
        $serializerMock
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn(new CompanyDtoV1());

        $validatorMock = $this->getMockBuilder(ValidatorInterface::class)->getMock();
        $validatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList([new ConstraintViolation('Invalid field value', '', [], null, '', 1)]));

        $service = $this->getCompanyService($repositoryMock, null, $serializerMock, $validatorMock);

        $this->expectException(CompanyCreateValidationException::class);
        $this->expectExceptionMessage('Company entry cannot created due to validation errors: Invalid field value');
        $service->createFromJson($json);
    }

    public function testAddCompanyFromJsonValidSuccess(): void
    {
        $json = file_get_contents(sprintf('/%s/new-company-data.json', TESTS_FIXTURES_PATH));
        assert(is_string($json));

        $repositoryMock = $this->getMockBuilder(CompanyRepositoryInterface::class)->getMock();
        $repositoryMock
            ->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf(Company::class), true);

        $serializerMock = $this->getMockBuilder(SerializerInterface::class)->getMock();
        $testCompanyDto = $this->createTestCompanyDto();
        $serializerMock
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($testCompanyDto);

        $validatorMock = $this->getMockBuilder(ValidatorInterface::class)->getMock();
        $validatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $service = $this->getCompanyService($repositoryMock, null, $serializerMock, $validatorMock);
        $newCompany = $service->createFromJson($json);
        $this->assertInstanceOf(Company::class, $newCompany);
        $this->assertSame($newCompany->getName(), $testCompanyDto->getName());
    }

    private function getCompanyService(
        CompanyRepositoryInterface $repositoryMock = null,
        InvoiceRepositoryInterface $invoiceRepositoryMock = null,
        SerializerInterface $serializerMock = null,
        ValidatorInterface $validatorMock = null,
    ): CompanyService {
        return new CompanyService(
            $repositoryMock ?: $this->createMock(CompanyRepositoryInterface::class),
            $invoiceRepositoryMock ?: $this->createMock(InvoiceRepositoryInterface::class),
            $serializerMock ?: $this->createMock(SerializerInterface::class),
            $validatorMock ?: $this->createMock(ValidatorInterface::class)
        );
    }
}
