<?php

namespace App\Http\Controller;

use App\Domain\Service\TotemService;
use App\Http\Request\Totem\CreateTotemRequest;
use App\Http\Request\Totem\UpdateTotemRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\TotemResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/totems', name: 'api_totems_')]
final class TotemController extends AbstractController
{
    public function __construct(
        private readonly TotemService $totemService,
        private readonly TotemResponse $totemResponse,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return ApiResponseFactory::success(
            data: $this->totemResponse->collection($this->totemService->listAll()),
            message: 'Totems list retrieved.'
        );
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $totem = $this->totemService->getTotemOrFail($id);

        return ApiResponseFactory::success(
            data: $this->totemResponse->item($totem),
            message: 'Totem retrieved.'
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = CreateTotemRequest::fromHttpRequest($request);

        $totem = $this->totemService->createTotem(
            name: $dto->getName(),
            description: $dto->getDescription(),
            picture: $dto->getPicture()
        );

        return ApiResponseFactory::success(
            data: $this->totemResponse->item($totem),
            message: 'Totem created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $dto = UpdateTotemRequest::fromHttpRequest($request);

        $totem = $this->totemService->updateTotem(
            totemId: $id,
            name: $dto->getName(),
            description: $dto->getDescription(),
            picture: $dto->getPicture()
        );

        return ApiResponseFactory::success(
            data: $this->totemResponse->item($totem),
            message: 'Totem updated successfully.'
        );
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->totemService->deleteTotem($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Totem deleted successfully.'
        );
    }
}
