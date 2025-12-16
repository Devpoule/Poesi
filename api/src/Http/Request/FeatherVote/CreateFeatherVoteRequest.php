<?php

namespace App\Http\Request\FeatherVote;

use App\Domain\Enum\FeatherType;
use App\Http\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Validates and normalizes the payload for creating/updating a FeatherVote.
 *
 * Expected JSON body:
 * {
 *   "voterAuthorId": 1,
 *   "poemId": 10,
 *   "featherType": "gold"
 * }
 */
final class CreateFeatherVoteRequest
{
    private int $voterAuthorId;
    private int $poemId;
    private FeatherType $featherType;

    private function __construct(int $voterAuthorId, int $poemId, FeatherType $featherType)
    {
        $this->voterAuthorId = $voterAuthorId;
        $this->poemId = $poemId;
        $this->featherType = $featherType;
    }

    /**
     * @param Request $request
     *
     * @throws ValidationException
     */
    public static function fromHttpRequest(Request $request): self
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            throw ValidationException::fromErrors(
                message: 'Invalid JSON payload.',
                errors: ['body' => ['Request body must be valid JSON.']]
            );
        }

        $errors = [];

        $voterAuthorId = $payload['voterAuthorId'] ?? null;
        $poemId = $payload['poemId'] ?? null;
        $featherRaw = $payload['featherType'] ?? null;

        if ($voterAuthorId === null || !is_numeric($voterAuthorId) || (int) $voterAuthorId <= 0) {
            $errors['voterAuthorId'][] = 'voterAuthorId must be a positive integer.';
        }

        if ($poemId === null || !is_numeric($poemId) || (int) $poemId <= 0) {
            $errors['poemId'][] = 'poemId must be a positive integer.';
        }

        $featherType = null;
        if ($featherRaw === null || trim((string) $featherRaw) === '') {
            $errors['featherType'][] = 'featherType is required.';
        } else {
            try {
                $featherType = FeatherType::from((string) $featherRaw);
            } catch (\ValueError) {
                $errors['featherType'][] = 'featherType is invalid.';
            }
        }

        if (!empty($errors)) {
            throw ValidationException::fromErrors(
                message: 'Invalid feather vote payload.',
                errors: $errors
            );
        }

        return new self(
            voterAuthorId: (int) $voterAuthorId,
            poemId: (int) $poemId,
            featherType: $featherType
        );
    }

    public function getVoterAuthorId(): int
    {
        return $this->voterAuthorId;
    }

    public function getPoemId(): int
    {
        return $this->poemId;
    }

    public function getFeatherType(): FeatherType
    {
        return $this->featherType;
    }
}
