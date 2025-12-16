<?php

namespace App\Http\Request\Reward;

use App\Http\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Validates payload for updating a Reward.
 */
final class UpdateRewardRequest
{
    private ?string $code;
    private ?string $label;

    private function __construct(?string $code, ?string $label)
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
        $hasAny = false;

        $code = null;
        if (array_key_exists('code', $payload)) {
            $hasAny = true;
            $code = trim((string) $payload['code']);
            if ($code === '') {
                $errors['code'][] = 'code cannot be empty.';
            }
        }

        $label = null;
        if (array_key_exists('label', $payload)) {
            $hasAny = true;
            $label = trim((string) $payload['label']);
            if ($label === '') {
                $errors['label'][] = 'label cannot be empty.';
            }
        }

        if ($hasAny === false) {
            $errors['payload'][] = 'At least one field must be provided.';
        }

        if (!empty($errors)) {
            throw ValidationException::fromErrors('Invalid reward payload.', $errors);
        }

        return new self($code, $label);
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }
}
