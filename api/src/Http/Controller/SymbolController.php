<?php

namespace App\Http\Controller;

use App\Domain\Service\SymbolService;
use App\Http\Request\Pagination;
use App\Http\Request\Symbol\CreateSymbolRequest;
use App\Http\Request\Symbol\UpdateSymbolRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\SymbolResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/symbols', name: 'api_symbols_')]
final class SymbolController extends AbstractController
{
    public function __construct(
        private readonly SymbolService $symbolService,
        private readonly SymbolResponse $symbolResponse,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $pagination = Pagination::fromRequest(
            $request,
            allowedSorts: ['id' => 'id', 'key' => 'key', 'label' => 'label'],
            defaultSort: 'label',
            defaultDirection: 'ASC',
            defaultLimit: 100,
            maxLimit: 200
        );

        $total = $this->symbolService->countSymbols();

        return ApiResponseFactory::success(
            data: $this->symbolResponse->collection(
                $this->symbolService->listPage(
                    $pagination->getLimit(),
                    $pagination->getOffset(),
                    $pagination->getSort(),
                    $pagination->getDirection()
                )
            ),
            message: 'Symbols list retrieved.',
            meta: [
                'pagination' => [
                    'page' => $pagination->getPage(),
                    'limit' => $pagination->getLimit(),
                    'total' => $total,
                    'pages' => (int) ceil($total / max(1, $pagination->getLimit())),
                    'sort' => $pagination->getSort(),
                    'direction' => $pagination->getDirection(),
                ],
            ]
        );
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $symbol = $this->symbolService->getOrFail($id);

        return ApiResponseFactory::success(
            data: $this->symbolResponse->item($symbol),
            message: 'Symbol retrieved.'
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = CreateSymbolRequest::fromHttpRequest($request);

        $symbol = $this->symbolService->create(
            key: $dto->getKey(),
            label: $dto->getLabel(),
            description: $dto->getDescription(),
            picture: $dto->getPicture()
        );

        return ApiResponseFactory::success(
            data: $this->symbolResponse->item($symbol),
            message: 'Symbol created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $dto = UpdateSymbolRequest::fromHttpRequest($request);

        $symbol = $this->symbolService->update(
            id: $id,
            key: $dto->getKey(),
            label: $dto->getLabel(),
            description: $dto->getDescription(),
            picture: $dto->getPicture()
        );

        return ApiResponseFactory::success(
            data: $this->symbolResponse->item($symbol),
            message: 'Symbol updated successfully.'
        );
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->symbolService->delete($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Symbol deleted successfully.'
        );
    }
}
