<?php

namespace App\Http\Controller;

use App\Domain\Service\FeatherService;
use App\Http\Request\Pagination;
use App\Http\Request\Feather\CreateFeatherRequest;
use App\Http\Request\Feather\UpdateFeatherRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\FeatherResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/feathers', name: 'api_feathers_')]
final class FeatherController extends AbstractController
{
    public function __construct(
        private readonly FeatherService $featherService,
        private readonly FeatherResponse $featherResponse,
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

        $total = $this->featherService->countFeathers();

        return ApiResponseFactory::success(
            data: $this->featherResponse->collection(
                $this->featherService->listPage(
                    $pagination->getLimit(),
                    $pagination->getOffset(),
                    $pagination->getSort(),
                    $pagination->getDirection()
                )
            ),
            message: 'Feathers list retrieved.',
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
        $feather = $this->featherService->getOrFail($id);

        return ApiResponseFactory::success(
            data: $this->featherResponse->item($feather),
            message: 'Feather retrieved.'
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = CreateFeatherRequest::fromHttpRequest($request);

        $feather = $this->featherService->create(
            key: $dto->getKey(),
            label: $dto->getLabel(),
            description: $dto->getDescription(),
            icon: $dto->getIcon()
        );

        return ApiResponseFactory::success(
            data: $this->featherResponse->item($feather),
            message: 'Feather created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $dto = UpdateFeatherRequest::fromHttpRequest($request);

        $feather = $this->featherService->update(
            id: $id,
            key: $dto->getKey(),
            label: $dto->getLabel(),
            description: $dto->getDescription(),
            icon: $dto->getIcon()
        );

        return ApiResponseFactory::success(
            data: $this->featherResponse->item($feather),
            message: 'Feather updated successfully.'
        );
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->featherService->delete($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Feather deleted successfully.'
        );
    }
}
