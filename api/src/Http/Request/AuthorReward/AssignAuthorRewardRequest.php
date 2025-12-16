<?php

namespace App\Http\Request\AuthorReward;

use App\Http\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Validates and normalizes the payload for assigning a reward to an author.
 *
 * Expected JSON body:
 * {
 *   "rewardCode": "FIRST_POEM"
 * }
 */
final class AssignAuthorRewardRequest
{
    /**
     * @var string
     */
    private string $rewardCode;

    private function __construct(string $rewardCode)
    {
        $this->rewardCode = $rewardCode;
    }

    /**
     * Builds an AssignAuthorRewardRequest from an HTTP request.
     *
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

        $rewardCode = $payload['rewardCode'] ?? null;

        if ($rewardCode === null || trim((string) $rewardCode) === '') {
            $errors['rewardCode'][] = 'rewardCode is required.';
        }

        if (!empty($errors)) {
            throw ValidationException::fromErrors(
                message: 'Invalid reward assignment payload.',
                errors: $errors
            );
        }

        return new self(trim((string) $rewardCode));
    }

    /**
     * @return string
     */
    public function getRewardCode(): string
    {
        return $this->rewardCode;
    }
}
