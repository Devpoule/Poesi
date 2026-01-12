<?php

namespace App\Http\Controller;

use App\Domain\Service\TotemService;
use App\Http\Request\Pagination;
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
    public function list(Request $request): JsonResponse
    {
        $pagination = Pagination::fromRequest(
            $request,
            allowedSorts: ['id' => 'id', 'key' => 'key', 'name' => 'name'],
            defaultSort: 'name',
            defaultDirection: 'ASC',
            defaultLimit: 100,
            maxLimit: 200
        );

        $total = $this->totemService->countTotems();

        return ApiResponseFactory::success(
            data: $this->totemResponse->collection(
                $this->totemService->listPage(
                    $pagination->getLimit(),
                    $pagination->getOffset(),
                    $pagination->getSort(),
                    $pagination->getDirection()
                )
            ),
            message: 'Totems list retrieved.',
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
