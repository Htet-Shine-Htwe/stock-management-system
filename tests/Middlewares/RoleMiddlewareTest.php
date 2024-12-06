<?php

declare(strict_types=1);

namespace Tests\Middlewares;

use App\Middleware\AdminMiddleware;
use App\Middleware\UserMiddleware;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;
use App\Contracts\AuthInterface;
use App\Contracts\UserInterface;
use App\Entity\User;
use App\Enum\RoleEnum;
use Psr\Http\Message\ResponseFactoryInterface;

class RoleMiddlewareTest extends TestCase
{
    private RequestHandlerInterface $handler;
    private AuthInterface $auth;
    private ResponseFactoryInterface $responseFactory;

    protected function setUp(): void
    {
        // Mock the dependencies
        $this->handler = $this->createMock(RequestHandlerInterface::class);
        $this->handler->method('handle')->willReturn(new Response());

        $this->auth = $this->createMock(AuthInterface::class);
        $this->responseFactory = $this->createMock(ResponseFactoryInterface::class);
    }

    public function testAdminMiddlewareAllowsAdmin(): void
    {
        $middleware = new AdminMiddleware($this->auth, $this->responseFactory);

        $mockRole = $this->createMock(\App\Entity\Role::class);
        $mockRole->method('getName')->willReturn('Admin'); 

        $mockUser = $this->createMock(\App\Entity\User::class);
        $mockUser->method('getRole')->willReturn($mockRole);

        $this->auth
            ->expects($this->once())
            ->method('user')
            ->willReturn($mockUser);

        $request = (new ServerRequestFactory())->createServerRequest('GET', '/admin/products');
        $response = $middleware->process($request, $this->handler);

        $this->assertSame(200, $response->getStatusCode());
    }


    public function testAdminMiddlewareDeniesNonAdmin(): void
    {
        $middleware = new AdminMiddleware($this->auth, $this->responseFactory);

        $mockUser = $this->createMock(UserInterface::class);
        $mockUser->method('getRole')->willReturn('non-admin');

        $this->auth
            ->expects($this->once())
            ->method('user')
            ->willReturn($mockUser);

        $request = (new ServerRequestFactory())->createServerRequest('GET', '/admin/products');

        $this->expectException(HttpForbiddenException::class);
        $middleware->process($request, $this->handler);
    }

    public function testUserMiddlewareAllowsUser(): void
    {
        $middleware = new UserMiddleware($this->auth, $this->responseFactory);

        $mockRole = $this->createMock(\App\Entity\Role::class);
        $mockRole->method('getName')->willReturn('User'); 

        $mockUser = $this->createMock(\App\Entity\User::class);
        $mockUser->method('getRole')->willReturn($mockRole);

        $this->auth
            ->expects($this->once())
            ->method('user')
            ->willReturn($mockUser);

        $request = (new ServerRequestFactory())->createServerRequest('GET', '/products');
        $response = $middleware->process($request, $this->handler);

        $this->assertSame(200, $response->getStatusCode());
    }


    public function testUserMiddlewareDeniesNonUser(): void
    {
        $middleware = new UserMiddleware($this->auth, $this->responseFactory);

        $mockUser = $this->createMock(UserInterface::class);
        $mockUser->method('getRole')->willReturn('Admin');

        $this->auth
            ->expects($this->once())
            ->method('user')
            ->willReturn($mockUser);

        $request = (new ServerRequestFactory())->createServerRequest('GET', '/products');

        $this->expectException(HttpForbiddenException::class);
        $middleware->process($request, $this->handler);
    }
}
