<?php

namespace App\Http\Request\UserReward;

use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use App\Http\Request\RequestPayload;
use Symfony\Component\HttpFoundation\Request;

final class AssignUserRewardRequest
{
    private string $rewardCode;

    private function __construct(string $rewardCode)
    {
        $this->rewardCode = $rewardCode;
    }

    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors = [];

        $rewardCode = RequestPayload::getTrimmedString($payload, 'rewardCode');
        if ($rewardCode === null) {
            $errors['rewardCode'][] = 'RewardCode is required.';
        }

        if ($errors !== []) {
            throw ValidationException::fromErrors(
                message: 'Invalid reward assignment payload.',
                errors: $errors
            );
        }

        return new self($rewardCode);
    }

    public function getRewardCode(): string
    {
        return $this->rewardCode;
    }
}
