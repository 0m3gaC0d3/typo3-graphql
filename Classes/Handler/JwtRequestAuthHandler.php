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

namespace Wpu\Graphql\Handler;

use Psr\Http\Message\ServerRequestInterface;
use Wpu\Graphql\Auth\Jwt\JwtAuthInterface;
use Wpu\Graphql\Exception\HttpUnauthorizedException;

class JwtRequestAuthHandler implements RequestAuthHandlerInterface
{
    /**
     * @var JwtAuthInterface
     */
    private $jsonWebTokenAuth;

    public function __construct(JwtAuthInterface $jwtAuth)
    {
        $this->jsonWebTokenAuth = $jwtAuth;
    }

    public function handleRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        $authorization = explode(' ', $request->getHeaderLine('Authorization'));
        $token = $authorization[1] ?? '';
        if (!$token || !$this->jsonWebTokenAuth->validateToken($token)) {
            throw new HttpUnauthorizedException('Not authorized');
        }
        $parsedToken = $this->jsonWebTokenAuth->createParsedToken($token);

        return $request->withAttribute('token', $parsedToken);
    }
}
