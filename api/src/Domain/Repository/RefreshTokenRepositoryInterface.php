<?php

namespace App\Domain\Repository;

use App\Domain\Entity\RefreshToken;

/**
 * @extends EntityRepositoryInterface<RefreshToken>
 */
interface RefreshTokenRepositoryInterface extends EntityRepositoryInterface
{
    public function findOneByToken(string $token): ?RefreshToken;
}
