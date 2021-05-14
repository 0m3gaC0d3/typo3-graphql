<?php

/**
 * This file is part of the "typo3-graphql" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Wolf Utz <wpu@hotmail.de>
 */

declare(strict_types=1);

namespace Wpu\Graphql\Auth\Jwt;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;

interface JwtAuthInterface
{
    public function getLifetime(): int;

    public function createJwt(array $claims): string;

    public function createParsedToken(string $token): Token;

    public function validateToken(string $accessToken): bool;

    public function getSignerKey(): Key;

    public function getVerifyKey(): Key;

    public function getSigner(): Signer;
}
