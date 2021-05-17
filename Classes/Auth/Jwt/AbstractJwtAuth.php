<?php

/**
 * This file is part of the "wpu_graphql" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Wolf Utz <wpu@hotmail.de>
 */

declare(strict_types=1);

namespace Wpu\Graphql\Auth\Jwt;

use Cake\Chronos\Chronos;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Ramsey\Uuid\Uuid;

abstract class AbstractJwtAuth implements JwtAuthInterface
{
    /**
     * @var string string
     */
    protected $issuer = '';

    /**
     * @var int
     */
    protected $lifetime = 0;

    public function __construct(string $issuer, int $lifetime)
    {
        $this->issuer = $issuer;
        $this->lifetime = $lifetime;
    }

    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    public function createJwt(array $claims): string
    {
        $issuedAt = Chronos::now()->getTimestamp();
        $builder = (new Builder())->issuedBy($this->issuer)
            ->identifiedBy(Uuid::uuid4()->toString(), true)
            ->issuedAt($issuedAt)
            ->canOnlyBeUsedAfter($issuedAt)
            ->expiresAt($issuedAt + $this->lifetime);
        foreach ($claims as $name => $value) {
            $builder->withClaim($name, $value);
        }

        return (string) $builder->getToken($this->getSigner(), $this->getSignerKey());
    }

    public function createParsedToken(string $token): Token
    {
        return (new Parser())->parse($token);
    }

    public function validateToken(string $accessToken): bool
    {
        try {
            $token = $this->createParsedToken($accessToken);
            if (!$token->verify($this->getSigner(), $this->getVerifyKey()->getContent())) {
                // Token signature is not valid
                return false;
            }
            $data = new ValidationData();
            $data->setCurrentTime(Chronos::now()->getTimestamp());
            $data->setIssuer($token->getClaim('iss'));
            $data->setId($token->getClaim('jti'));
        } catch (\Exception $e) {
            return false;
        }

        return $token->validate($data);
    }
}
