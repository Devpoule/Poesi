<?php

namespace App\Http\Request\Reward;

use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use App\Http\Request\RequestPayload;
use Symfony\Component\HttpFoundation\Request;

/**
 * Parses and validates payload for creating a Reward.
 */
final class CreateRewardRequest
{
    private string $code;
    private string $label;

    private function __construct(string $code, string $label)
    {
        $this->code = $code;
        $this->label = $label;
    }

    /**
     * Expected JSON:
     * {
     *   "code": "FIRST_POEM",
     *   "label": "First poem published"
     * }
     *
     * @throws ValidationException
     */
    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors = [];

        $code = RequestPayload::getTrimmedString($payload, 'code');
        if ($code === null) {
            $errors['code'][] = 'Code is required.';
        }

        $label = RequestPayload::getTrimmedString($payload, 'label');
        if ($label === null) {
            $errors['label'][] = 'Label is required.';
        }

        if ($errors !== []) {
            throw ValidationException::fromErrors(
                message: 'Invalid reward payload.',
                errors: $errors
            );
        }

        return new self($code, $label);
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
