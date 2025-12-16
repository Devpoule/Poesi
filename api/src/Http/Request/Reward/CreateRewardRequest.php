<?php

namespace App\Http\Request\Reward;

use App\Http\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Validates payload for creating a Reward.
 */
final class CreateRewardRequest
{
    private string $code;
    private string $label;

    private function __construct(string $code, string $label)
    {
        $this->code  = $code;
        $this->label = $label;
    }

    public static function fromHttpRequest(Request $request): self
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            throw ValidationException::fromErrors(
                'Invalid JSON payload.',
                ['body' => ['Request body must be valid JSON.']]
            );
        }

        $errors = [];

        $code  = $payload['code']  ?? null;
        $label = $payload['label'] ?? null;

        if ($code === null || trim((string) $code) === '') {
            $errors['code'][] = 'code is required.';
        }

        if ($label === null || trim((string) $label) === '') {
            $errors['label'][] = 'label is required.';
        }

        if (!empty($errors)) {
            throw ValidationException::fromErrors('Invalid reward payload.', $errors);
        }

        return new self((string) $code, (string) $label);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
