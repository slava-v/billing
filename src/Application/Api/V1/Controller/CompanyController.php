<?php

declare(strict_types=1);

namespace Billing\Application\Api\V1\Controller;

use Billing\Infrastructure\Service\CompanyServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

class CompanyController extends AbstractController
{
    public function __construct(
        private readonly CompanyServiceInterface $companyService,
    ) {
    }

    #[Route('/v1/company/{name}/name', methods: 'GET', name: 'get_company_by_name_v1')]
    #[OA\Get(
        path: '/v1/company/{name}/name',
        responses: [
            new OA\Response(
                response: '200',
                description: 'Returned when company is found',
                content: new OA\JsonContent(ref: '#/components/schemas/Company')
            ),
            new OA\Response(
                response: '404',
                description: 'Returned when no company is available',
            )]
    )]
    public function getCompanyAction(string $name): JsonResponse
    {
        $data = $this->companyService->getCompanyByName($name);

        return $this->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    #[Route('/v1/company/{id}', methods: 'GET', name: 'get_company_by_id_v1')]
    public function getCompanyByIdAction(string $id): JsonResponse
    {
        $data = $this->companyService->getCompanyById(Uuid::fromString($id));

        return $this->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    #[Route('/v1/company', methods: 'PUT', name: 'add_company_v1')]
    #[OA\Put(
        path: '/v1/company/{name}/name',
        requestBody: new OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/CompanyAddRequest')),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Returned when company is found',
                content: new OA\JsonContent(ref: '#/components/schemas/Company')
            ),
            new OA\Response(
                response: '404',
                description: 'Returned when no company is available',
            )]
    )]
    public function createCompanyAction(Request $request): JsonResponse
    {
        /**
         * The deserialization from "addCompanyFromJson" could also be achieved automatically by using "ParamConverter"
         * from \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter
         * But, for sake of simplicity, let's deserialize & validate in a simple/straightforward way
         */

        $newCompany = $this->companyService->createFromJson($request->getContent());

        return $this->json([
            'success' => true,
            'data' => $newCompany,
        ]);
    }
}
