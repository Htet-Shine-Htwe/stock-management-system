<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Enum\RoleEnum;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AdminGuardMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly ResponseFactoryInterface $responseFactory)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $request->getAttribute('user');

        $role = $user?->getRole();

        if ($role->name == RoleEnum::Admin->value) {
            return $handler->handle($request);
        }

        return $this->responseFactory->createResponse(403);

    }
}
