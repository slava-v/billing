<?php

declare(strict_types=1);

namespace Billing\Application\Api\V1\Controller;

use Billing\Infrastructure\Service\InvoiceServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class InvoiceController extends AbstractController
{
    public function __construct(
        private readonly InvoiceServiceInterface $invoiceService
    ) {
    }

    #[Route('/v1/invoice/{id}', methods: 'GET', name: 'get_invoice_by_id_v1')]
    public function index(string $id): JsonResponse
    {
        $invoice = $this->invoiceService->getInvoiceById(Uuid::fromString($id));
        return $this->json([
            'success' => true,
            'data' => $invoice,
        ]);
    }

    #[Route('/v1/invoice', methods: 'PUT', name: 'add_invoice_v1')]
    public function add(Request $request): JsonResponse
    {
        $invoice = $this->invoiceService->createFromJson($request->getContent());

        return $this->json([
            'success' => true,
            'data' => $invoice,
        ]);
    }
}
