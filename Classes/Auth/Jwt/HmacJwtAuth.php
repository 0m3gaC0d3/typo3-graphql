<?php

declare(strict_types=1);

namespace Wpu\Graphql\Auth\Jwt;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

class HmacJwtAuth extends AbstractJwtAuth
{
    /**
     * @var string
     */
    private $key;

    public function __construct(string $issuer, int $lifetime, string $key)
    {
        parent::__construct($issuer, $lifetime);
        $this->key = $key;
    }

    public function getSignerKey(): Key
    {
        return new Key($this->key);
    }

    public function getVerifyKey(): Key
    {
        return new Key($this->key);
    }

    public function getSigner(): Signer
    {
        return new Sha256();
    }
}
