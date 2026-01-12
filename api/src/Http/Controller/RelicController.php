<?php

namespace App\Http\Controller;

use App\Domain\Service\RelicService;
use App\Http\Request\Pagination;
use App\Http\Request\Relic\CreateRelicRequest;
use App\Http\Request\Relic\UpdateRelicRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\RelicResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/relics', name: 'api_relics_')]
final class RelicController extends AbstractController
{
    public function __construct(
        private readonly RelicService $relicService,
        private readonly RelicResponse $relicResponse,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $pagination = Pagination::fromRequest(
            $request,
            allowedSorts: ['id' => 'id', 'key' => 'key', 'label' => 'label', 'rarity' => 'rarity'],
            defaultSort: 'rarity',
            defaultDirection: 'DESC',
            defaultLimit: 100,
            maxLimit: 200
        );

        $total = $this->relicService->countRelics();

        return ApiResponseFactory::success(
            data: $this->relicResponse->collection(
                $this->relicService->listPage(
                    $pagination->getLimit(),
                    $pagination->getOffset(),
                    $pagination->getSort(),
                    $pagination->getDirection()
                )
            ),
            message: 'Relics list retrieved.',
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
        $relic = $this->relicService->getOrFail($id);

        return ApiResponseFactory::success(
            data: $this->relicResponse->item($relic),
            message: 'Relic retrieved.'
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = CreateRelicRequest::fromHttpRequest($request);

        $relic = $this->relicService->create(
            key: $dto->getKey(),
            label: $dto->getLabel(),
            rarity: $dto->getRarity(),
            description: $dto->getDescription(),
            picture: $dto->getPicture()
        );

        return ApiResponseFactory::success(
            data: $this->relicResponse->item($relic),
            message: 'Relic created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $dto = UpdateRelicRequest::fromHttpRequest($request);

        $relic = $this->relicService->update(
            id: $id,
            key: $dto->getKey(),
            label: $dto->getLabel(),
            rarity: $dto->getRarity(),
            description: $dto->getDescription(),
            picture: $dto->getPicture()
        );

        return ApiResponseFactory::success(
            data: $this->relicResponse->item($relic),
            message: 'Relic updated successfully.'
        );
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->relicService->delete($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Relic deleted successfully.'
        );
    }
}
