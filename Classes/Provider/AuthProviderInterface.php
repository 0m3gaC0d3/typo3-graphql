<?php


namespace Wpu\Graphql\Provider;

use Psr\Http\Message\ServerRequestInterface;

interface AuthProviderInterface
{
    public function handleRequest(ServerRequestInterface $request): ServerRequestInterface;
}
