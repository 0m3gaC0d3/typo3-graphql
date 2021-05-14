<?php

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
