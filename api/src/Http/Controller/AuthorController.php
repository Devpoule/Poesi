<?php

namespace App\Http\Controller;

use App\Domain\Service\AuthorService;
use App\Http\Request\Author\CreateAuthorRequest;
use App\Http\Request\Author\HatchAuthorRequest;
use App\Http\Request\Author\UpdateAuthorRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\AuthorResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/authors', name: 'api_authors_')]
final class AuthorController extends AbstractController
{
    public function __construct(
        private readonly AuthorService $authorService,
        private readonly AuthorResponse $authorResponse,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return ApiResponseFactory::success(
            data: $this->authorResponse->collection($this->authorService->listAll()),
            message: 'Authors list retrieved.'
        );
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $author = $this->authorService->getAuthorOrFail($id);

        return ApiResponseFactory::success(
            data: $this->authorResponse->item($author),
            message: 'Author retrieved.'
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = CreateAuthorRequest::fromHttpRequest($request);

        $author = $this->authorService->createAuthor(
            pseudo: $dto->getPseudo(),
            email: $dto->getEmail(),
            totemId: $dto->getTotemId(),
            moodColor: $dto->getMoodColor(),
            randomTotem: $dto->isRandomTotem()
        );

        return ApiResponseFactory::success(
            data: $this->authorResponse->item($author),
            message: 'Author created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $dto = UpdateAuthorRequest::fromHttpRequest($request);

        $author = $this->authorService->updateAuthor(
            authorId: $id,
            pseudo: $dto->getPseudo(),
            moodColor: $dto->getMoodColor(),
            totemId: $dto->getTotemId()
        );

        return ApiResponseFactory::success(
            data: $this->authorResponse->item($author),
            message: 'Author updated successfully.'
        );
    }

    #[Route('/{id<\d+>}/hatch', name: 'hatch', methods: ['POST'])]
    public function hatch(int $id, Request $request): JsonResponse
    {
        $dto = HatchAuthorRequest::fromHttpRequest($request);

        $author = $this->authorService->updateAuthor(
            authorId: $id,
            totemId: $dto->getTotemId()
        );

        return ApiResponseFactory::success(
            data: $this->authorResponse->item($author),
            message: 'Author hatched successfully.'
        );
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->authorService->deleteAuthor($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Author deleted successfully.'
        );
    }
}
