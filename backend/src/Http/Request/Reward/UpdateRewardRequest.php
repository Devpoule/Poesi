<?php

namespace App\Http\Request\Reward;

use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use App\Http\Request\RequestPayload;
use Symfony\Component\HttpFoundation\Request;

/**
 * Parses and validates payload for updating a Reward.
 *
 * All fields are optional, but at least one must be provided.
 */
final class UpdateRewardRequest
{
    private ?string $code;
    private ?string $label;

    private function __construct(?string $code, ?string $label)
    {
        $this->code = $code;
        $this->label = $label;
    }

    /**
     * Expected JSON (at least one key):
     * {
     *   "code": "NEW_CODE",
     *   "label": "New label"
     * }
     *
     * @throws ValidationException
     */
    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors = [];
        $hasAny = false;

        $code = null;
        if (\array_key_exists('code', $payload)) {
            $hasAny = true;
            $code = RequestPayload::getTrimmedString($payload, 'code');
            if ($code === null) {
                $errors['code'][] = 'code cannot be empty when provided.';
            }
        }

        $label = null;
        if (\array_key_exists('label', $payload)) {
            $hasAny = true;
            $label = RequestPayload::getTrimmedString($payload, 'label');
            if ($label === null) {
                $errors['label'][] = 'label cannot be empty when provided.';
            }
        }

        if ($hasAny === false) {
            $errors['payload'][] = 'At least one field must be provided.';
        }

        if ($errors !== []) {
            throw ValidationException::fromErrors(
                message: 'Invalid reward payload.',
                errors: $errors
            );
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
