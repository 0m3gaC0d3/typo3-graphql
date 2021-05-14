<?php


namespace Wpu\Graphql\Action;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wpu\Graphql\Auth\Jwt\JwtAuthInterface;

class LoginAction implements ActionInterface
{
    /**
     * @var JwtAuthInterface
     */
    private $auth;

    public function __construct(JwtAuthInterface $auth)
    {
        $this->auth = $auth;
    }

    public function process(ServerRequestInterface $request, ResponseInterface $response, array $configuration = []): ResponseInterface
    {
        $result = [
            'access_token' => $this->auth->createJwt([]),
            'token_type' => 'Bearer',
            'expires_in' => $this->auth->getLifetime(),
        ];
        $response->getBody()->write((string) json_encode($result));
        $response = $response->withStatus(201)->withHeader('Content-type', 'application/json');

        return $response;
    }
}
