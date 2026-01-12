<?php

namespace App\Http\Request\FeatherVote;

use App\Domain\Enum\FeatherType;
use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use App\Http\Request\RequestPayload;
use Symfony\Component\HttpFoundation\Request;

final class CreateFeatherVoteRequest
{
    private ?int $voterUserId;
    private int $poemId;
    private FeatherType $featherType;

    private function __construct(?int $voterUserId, int $poemId, FeatherType $featherType)
    {
        $this->voterUserId = $voterUserId;
        $this->poemId = $poemId;
        $this->featherType = $featherType;
    }

    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors = [];

        $voterUserId = null;
        if (\array_key_exists('voterUserId', $payload)) {
            if ($payload['voterUserId'] !== null) {
                $voterUserId = RequestPayload::getPositiveInt($payload, 'voterUserId');
                if ($voterUserId === null) {
                    $errors['voterUserId'][] = 'VoterUserId must be a positive integer.';
                }
            }
        }

        $poemId = RequestPayload::getPositiveInt($payload, 'poemId');
        if ($poemId === null) {
            $errors['poemId'][] = 'PoemId must be a positive integer.';
        }

        $featherRaw = RequestPayload::getTrimmedString($payload, 'featherType');
        $featherType = null;

        if ($featherRaw === null) {
            $errors['featherType'][] = 'FeatherType is required.';
        } else {
            try {
                $featherType = FeatherType::from($featherRaw);
            } catch (\ValueError) {
                $errors['featherType'][] = 'FeatherType is invalid.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::fromErrors(
                message: 'Invalid feather vote payload.',
                errors: $errors
            );
        }

        /** @var FeatherType $featherType */
        return new self($voterUserId, $poemId, $featherType);
    }

    public function getVoterUserId(): ?int
    {
        return $this->voterUserId;
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
