<?php

namespace App\Http\Controller;

use App\Domain\Lore\LoreCatalog;
use App\Http\Response\ApiResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Exposes canonical lore dictionaries to the frontend.
 *
 * This endpoint is read-only and returns UI-ready, indexed data structures.
 */
#[Route('/api/lore', name: 'api_lore_')]
final class LoreController extends AbstractController
{
    public function __construct(
        private readonly LoreCatalog $loreCatalog,
    ) {
    }

    #[Route('', name: 'get', methods: ['GET'])]
    public function get(): JsonResponse
    {
        $response = ApiResponseFactory::success(
            data: [
                'moods'    => $this->loreCatalog->getAllMoods(),
                'feathers' => $this->loreCatalog->getAllFeathers(),
                'symbols'  => $this->loreCatalog->getAllSymbols(),
                'relics'   => $this->loreCatalog->getAllRelics(),
            ],
            message: null,
            code: 'LORE_OK'
        );

        // Optional: simple caching for read-only reference data
        $response->setPublic();
        $response->setMaxAge(3600);

        return $response;
    }
}
