<?php

namespace App\Http\Controller;

use App\Domain\Exception\CannotDelete\CannotDeletePoemWithVotesException;
use App\Domain\Exception\CannotPublish\CannotPublishPoemException;
use App\Domain\Exception\CannotUpdate\CannotUpdatePoemException;
use App\Domain\Exception\NotFound\PoemNotFoundException;
use App\Domain\Service\PoemService;
use App\Http\Request\Pagination;
use App\Http\Request\Poem\CreatePoemRequest;
use App\Http\Request\Poem\UpdatePoemRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\PoemListResponse;
use App\Http\Response\PoemResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Domain\Entity\User;

#[Route('/api/poems', name: 'api_poems_')]
final class PoemController extends AbstractController
{
    public function __construct(
        private readonly PoemService $poemService,
        private readonly PoemListResponse $poemListResponse,
        private readonly PoemResponse $poemResponse,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        try {
            $pagination = Pagination::fromRequest(
                $request,
                allowedSorts: [
                    'id' => 'id',
                    'title' => 'title',
                    'status' => 'status',
                    'createdAt' => 'createdAt',
                    'publishedAt' => 'publishedAt',
                ],
                defaultSort: 'createdAt',
                defaultDirection: 'DESC',
                defaultLimit: 20,
                maxLimit: 100
            );

            $total = $this->poemService->countPoems();

            return ApiResponseFactory::success(
                data: $this->poemListResponse->collection(
                    $this->poemService->listPage(
                        $pagination->getLimit(),
                        $pagination->getOffset(),
                        $pagination->getSort(),
                        $pagination->getDirection()
                    )
                ),
                message: 'Poems list retrieved.',
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
        } catch (\Throwable) {
            return ApiResponseFactory::error(
                message: 'Unexpected server error.',
                code: 'UNEXPECTED_ERROR',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/full', name: 'list_full', methods: ['GET'])]
    public function listFull(Request $request): JsonResponse
    {
        try {
            $pagination = Pagination::fromRequest(
                $request,
                allowedSorts: [
                    'id' => 'id',
                    'title' => 'title',
                    'status' => 'status',
                    'createdAt' => 'createdAt',
                    'publishedAt' => 'publishedAt',
                ],
                defaultSort: 'createdAt',
                defaultDirection: 'DESC',
                defaultLimit: 20,
                maxLimit: 100
            );

            $total = $this->poemService->countPoems();

            return ApiResponseFactory::success(
                data: $this->poemResponse->collection(
                    $this->poemService->listFullPage(
                        $pagination->getLimit(),
                        $pagination->getOffset(),
                        $pagination->getSort(),
                        $pagination->getDirection()
                    )
                ),
                message: 'Poems list retrieved.',
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
        } catch (\Throwable) {
            return ApiResponseFactory::error(
                message: 'Unexpected server error.',
                code: 'UNEXPECTED_ERROR',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $poem = $this->poemService->getPoemOrFail($id);

            return ApiResponseFactory::success(
                data: $this->poemResponse->item($poem),
                message: 'Poem retrieved.'
            );
        } catch (PoemNotFoundException $e) {
            return ApiResponseFactory::notFound($e->getMessage());
        } catch (\Throwable) {
            return ApiResponseFactory::error(
                message: 'Unexpected server error.',
                code: 'UNEXPECTED_ERROR',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $currentUser = $this->getUser();
            if (!$currentUser instanceof User) {
                return ApiResponseFactory::error(
                    message: 'Authentication required.',
                    code: 'UNAUTHORIZED',
                    type: 'error',
                    errors: null,
                    data: null,
                    httpStatus: Response::HTTP_UNAUTHORIZED
                );
            }

            $dto = CreatePoemRequest::fromHttpRequest($request);
            if ($dto->getUserId() !== null && $dto->getUserId() !== $currentUser->getId()) {
                return ApiResponseFactory::error(
                    message: 'Cannot create poem for another user.',
                    code: 'FORBIDDEN',
                    type: 'error',
                    errors: null,
                    data: null,
                    httpStatus: Response::HTTP_FORBIDDEN
                );
            }

            $poem = $this->poemService->createDraft(
                userId: (int) $currentUser->getId(),
                title: $dto->getTitle(),
                content: $dto->getContent(),
                moodColor: $dto->getMoodColor()
            );

            return ApiResponseFactory::success(
                data: $this->poemResponse->item($poem),
                message: 'Poem created successfully.',
                httpStatus: Response::HTTP_CREATED
            );
        } catch (\Throwable $e) {
            // NB: si CreatePoemRequest lève ValidationException,
            // tu peux ajouter un catch dédié pour retourner un 422 propre.
            return ApiResponseFactory::error(
                message: $e->getMessage(),
                code: 'REQUEST_FAILED',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $poem = $this->poemService->getPoemOrFail($id);
            $this->denyAccessUnlessGranted('POEM_EDIT', $poem);
            $dto = UpdatePoemRequest::fromHttpRequest($request);

            $poem = $this->poemService->updatePoem(
                poemId: $id,
                title: $dto->getTitle(),
                content: $dto->getContent(),
                moodColor: $dto->getMoodColor()
            );

            return ApiResponseFactory::success(
                data: $this->poemResponse->item($poem),
                message: 'Poem updated successfully.'
            );
        } catch (PoemNotFoundException $e) {
            return ApiResponseFactory::notFound($e->getMessage());
        } catch (CannotUpdatePoemException $e) {
            return ApiResponseFactory::error(
                message: $e->getMessage(),
                code: $e->getErrorCode(),
                type: 'warning',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_CONFLICT
            );
        } catch (\Throwable $e) {
            return ApiResponseFactory::error(
                message: $e->getMessage(),
                code: 'REQUEST_FAILED',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('/{id<\d+>}/publish', name: 'publish', methods: ['POST'])]
    public function publish(int $id): JsonResponse
    {
        try {
            $poem = $this->poemService->getPoemOrFail($id);
            $this->denyAccessUnlessGranted('POEM_PUBLISH', $poem);
            $poem = $this->poemService->publish($id);

            return ApiResponseFactory::success(
                data: $this->poemResponse->item($poem),
                message: 'Poem published successfully.'
            );
        } catch (PoemNotFoundException $e) {
            return ApiResponseFactory::notFound($e->getMessage());
        } catch (CannotPublishPoemException $e) {
            return ApiResponseFactory::error(
                message: $e->getMessage(),
                code: $e->getErrorCode(),
                type: 'warning',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_CONFLICT
            );
        } catch (\Throwable) {
            return ApiResponseFactory::error(
                message: 'Unexpected server error.',
                code: 'UNEXPECTED_ERROR',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $poem = $this->poemService->getPoemOrFail($id);
            $this->denyAccessUnlessGranted('POEM_DELETE', $poem);
            $this->poemService->deletePoem($id);

            return ApiResponseFactory::success(
                data: null,
                message: 'Poem deleted successfully.'
            );
        } catch (PoemNotFoundException $e) {
            return ApiResponseFactory::notFound($e->getMessage());
        } catch (CannotDeletePoemWithVotesException $e) {
            return ApiResponseFactory::error(
                message: $e->getMessage(),
                code: $e->getErrorCode(),
                type: 'warning',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_CONFLICT
            );
        } catch (\Throwable) {
            return ApiResponseFactory::error(
                message: 'Unexpected server error.',
                code: 'UNEXPECTED_ERROR',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
