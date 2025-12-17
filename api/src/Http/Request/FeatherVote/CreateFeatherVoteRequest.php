<?php

namespace App\Http\Request\FeatherVote;

use App\Domain\Enum\FeatherType;
use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use App\Http\Request\RequestPayload;
use Symfony\Component\HttpFoundation\Request;

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

    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors = [];

        $voterAuthorId = RequestPayload::getPositiveInt($payload, 'voterAuthorId');
        if ($voterAuthorId === null) {
            $errors['voterAuthorId'][] = 'VoterAuthorId must be a positive integer.';
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
        return new self($voterAuthorId, $poemId, $featherType);
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
