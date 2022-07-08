<?php

declare(strict_types=1);

namespace Billie\Tests\Integration\Application\Api\V1\Controller;

use Billie\DataLayer\Entity\Company;
use Billie\DataLayer\Repository\CompanyRepositoryInterface;
use Billie\Tests\Integration\WebTestCase;
use Billie\Tests\TestTrait\TestCompanyTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CompanyControllerTest extends WebTestCase
{
    use TestCompanyTrait;

    private static ?Company $testCompany;

    public function testGetCompanyByNameActionCompanyDoesNotExistNotFound(): void
    {
        $url = $this
            ->getService(UrlGeneratorInterface::class)
            ->generate('get_company_by_name_v1', ['name' => 'random-foo-string']);

        $this->getClient()->request('GET', $url);

        $response = $this->getClient()->getResponse();
        $responseContent = $response->getContent() ?: '';

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($responseContent);

        $this->assertJsonStringEqualsJsonString(
            '{
          "success":false,
          "error":"Company random-foo-string not found. For more details refer to documentation or contact customer support"
             }',
            $responseContent
        );
    }

    public function testGetCompanyByNameActionCompanyExistsSuccess(): void
    {
        $url = $this
            ->getService(UrlGeneratorInterface::class)
            ->generate('get_company_by_name_v1', ['name' => $this->getTestCompany()->getName()]);

        $this->getClient()->request('GET', $url);

        $response = $this->getClient()->getResponse();
        $responseContent = $response->getContent() ?: '';

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($responseContent);

        /** @var array<string, array<string, string>> $decodedContent */
        $decodedContent = json_decode($responseContent, true);
        $this->assertTrue($decodedContent['success']);
        $this->assertSame($this->getTestCompany()->getName(), $decodedContent['data']['name']);
    }

    public function testGetCompanyByIdActionCompanyExistsSuccess(): void
    {
        $url = $this
            ->getService(UrlGeneratorInterface::class)
            ->generate('get_company_by_id_v1', ['id' => $this->getTestCompany()->getIdAsString()]);

        $this->getClient()->request('GET', $url);

        $response = $this->getClient()->getResponse();
        $responseContent = $response->getContent() ?: '';

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($responseContent);

        /** @var array<string, array<string, string>> $decodedContent */
        $decodedContent = json_decode($responseContent, true);
        $this->assertTrue($decodedContent['success']);
        $this->assertSame($this->getTestCompany()->getName(), $decodedContent['data']['name']);
    }

    public function testGetCompanyByIdActionUnknownEntryNotFound(): void
    {
        $fakeUuid = $this->getFaker()->uuid();
        $url = $this
            ->getService(UrlGeneratorInterface::class)
            ->generate('get_company_by_id_v1', ['id' => $fakeUuid]);

        $this->getClient()->request('GET', $url);

        $response = $this->getClient()->getResponse();
        $responseContent = $response->getContent() ?: '';

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($responseContent);
        $expectedJson = <<<JSON
        {
          "success":false,
          "error":"Company $fakeUuid not found. For more details refer to documentation or contact customer support"
        }
        JSON;

        $this->assertJsonStringEqualsJsonString(
            $expectedJson,
            $responseContent
        );
    }

    public function testCreateCompanyActionSuccess(): void
    {
        $url = $this
            ->getService(UrlGeneratorInterface::class)
            ->generate('add_company_v1');

        $json = file_get_contents(sprintf('/%s/new-company-data.json', TESTS_FIXTURES_PATH));
        assert(is_string($json));

        $this->getClient()->request('PUT', $url, content: $json);

        $response = $this->getClient()->getResponse();
        $responseContent = $response->getContent() ?: '';

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($responseContent);

        /** @var array<string, array<string, string>> $decodedContent */
        $decodedContent = json_decode($responseContent, true);
        $this->assertTrue($decodedContent['success']);
        $this->assertSame("Gottfried-Schade-Allee 12\n16038 Karlsfeld", $decodedContent['data']['address']);
    }

    public function testCreateCompanyInvalidStatusSuccess(): void
    {
        $url = $this
            ->getService(UrlGeneratorInterface::class)
            ->generate('add_company_v1');

        $json = file_get_contents(sprintf('/%s/new-company-data-wrong-status-code.json', TESTS_FIXTURES_PATH));
        assert(is_string($json));

        $this->getClient()->request('PUT', $url, content: $json);

        $response = $this->getClient()->getResponse();
        $responseContent = $response->getContent() ?: '';

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJson($responseContent);
        $this->assertSame(
            '{"success":false,"error":"Company cannot be created due to validation errors"}',
            $responseContent
        );
    }


    /**
     * Create reusable database entry
     * A @dataProvider or setUp() would create a new entry for each test, but this static property once created, it would
     * be reused throughout this class tests. We don't modify the db record, so it should be safe here.
     */
    private function getTestCompany(): Company
    {
        if (!isset(self::$testCompany)) {
            self::$testCompany = $this->createTestCompany();
            $companyRepository = $this->getService(CompanyRepositoryInterface::class);
            $companyRepository->add(self::$testCompany, true);
        }

        return self::$testCompany;
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::$testCompany = null;
    }
}
