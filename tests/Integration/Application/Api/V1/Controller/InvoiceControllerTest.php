<?php

declare(strict_types=1);

namespace Billie\Tests\Integration\Application\Api\V1\Controller;

use Billie\Application\Api\V1\Controller\InvoiceController;
use Billie\DataLayer\Entity\Invoice;
use Billie\DataLayer\Enums\InvoiceStatus;
use Billie\DataLayer\Repository\CompanyRepositoryInterface;
use Billie\DataLayer\Repository\InvoiceRepositoryInterface;
use Billie\Tests\Integration\WebTestCase;
use Billie\Tests\TestTrait\TestCompanyTrait;
use Billie\Tests\TestTrait\TestInvoiceTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class InvoiceControllerTest extends WebTestCase
{
    use TestCompanyTrait;
    use TestInvoiceTrait;

    public function testAddDebtorNotFoundException(): void
    {
        $url = $this
            ->getService(UrlGeneratorInterface::class)
            ->generate('add_invoice_v1');

        $fakeUuid = '17c72303-d775-3838-ad2d-6d5f3d4336de'; // See json file
        $json = file_get_contents(sprintf('/%s/new-invoice-data.json', TESTS_FIXTURES_PATH));
        assert(is_string($json));

        $this->getClient()->request('PUT', $url, content: $json);

        $response = $this->getClient()->getResponse();
        $responseContent = $response->getContent() ?: '';

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($responseContent);

        $expectedJson = <<<JSON
{"success":false,
"error":"Company $fakeUuid not found. For more details refer to documentation or contact customer support"}
JSON;

        $this->assertJsonStringEqualsJsonString($expectedJson, $responseContent);
    }

    public function testAddSuccess(): void
    {
        // Get 2 random companies and generate an "invoice add" request payload
        $companyRepository = $this->getService(CompanyRepositoryInterface::class);
        list($debtor, $creditor) = $companyRepository->findBy([], [], 2);
        $newInvoice = $this->createTestInvoiceDto($debtor->getId(), $creditor->getId());
        $serializer = $this->getService(SerializerInterface::class);
        $json = $serializer->serialize($newInvoice, 'json');

        $url = $this
            ->getService(UrlGeneratorInterface::class)
            ->generate('add_invoice_v1');

        $this->getClient()->request('PUT', $url, content: $json);

        $response = $this->getClient()->getResponse();
        $responseContent = $response->getContent() ?: '';

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($responseContent);

        /** @var array<string, array<string, string>> $decodedContent */
        $decodedContent = json_decode($responseContent, true);
        $this->assertTrue($decodedContent['success']);
        $this->assertSame(InvoiceStatus::OPEN->value, $decodedContent['data']['status']);
    }

    public function testIndexSuccess(): void
    {
        list($testInvoice) = $this->getService(InvoiceRepositoryInterface::class)->findBy([], [], 1);
        assert($testInvoice instanceof Invoice);

        $url = $this
            ->getService(UrlGeneratorInterface::class)
            ->generate('get_invoice_by_id_v1', ['id' => $testInvoice->getId()]);

        $this->getClient()->request('GET', $url);

        $response = $this->getClient()->getResponse();
        $responseContent = $response->getContent() ?: '';

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($responseContent);

        /** @var array<string, array<string, string>> $decodedContent */
        $decodedContent = json_decode($responseContent, true);
        $this->assertTrue($decodedContent['success']);
        $this->assertSame($testInvoice->getIdAsString(), $decodedContent['data']['id']);
    }
}
