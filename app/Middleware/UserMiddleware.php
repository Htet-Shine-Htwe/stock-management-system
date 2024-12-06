<?php

namespace App\Middleware;

use App\Contracts\AuthInterface;
use App\Entity\User;
use App\Enum\RoleEnum;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpForbiddenException;

class UserMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly AuthInterface $auth,
        private readonly ResponseFactoryInterface $responseFactory,
    ) {
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->auth->user();

        if ($user instanceof User && $user->getRole()->getName() === RoleEnum::User->value) {
            return $handler->handle($request);
        }

        // Forbidden if the user is not an admin
        throw new HttpForbiddenException($request, 'Access denied');
        
        // return $this->responseFactory->createResponse(403); // Forbidden
    }
}
