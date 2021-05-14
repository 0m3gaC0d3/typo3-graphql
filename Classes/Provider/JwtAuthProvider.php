<?php


namespace Wpu\Graphql\Provider;


use Psr\Http\Message\ServerRequestInterface;
use Wpu\Graphql\Auth\Jwt\JwtAuthInterface;

class JwtAuthProvider implements AuthProviderInterface
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
        $authorization = explode(' ', (string)$request->getHeaderLine('Authorization'));
        $token = $authorization[1] ?? '';
        if (!$token || !$this->jsonWebTokenAuth->validateToken($token)) {
            throw new \Exception("Not authorized", 401);
        }
        $parsedToken = $this->jsonWebTokenAuth->createParsedToken($token);
        $request = $request->withAttribute('token', $parsedToken);

        return $request;
    }
}
