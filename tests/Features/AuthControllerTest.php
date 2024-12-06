<?php
namespace Tests\Features;

use App\Auth;
use App\Contracts\UserInterface;
use App\Controllers\AuthController;
use App\Contracts\RequestValidatorFactoryInterface;
use App\Entity\Role;
use App\Exception\ValidationException;
use App\Requests\UserLoginRequestValidator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class AuthControllerTest extends TestCase
{
    private $twigMock;
    private $validatorFactoryMock;
    private $authMock;
    private $controller;

    protected function setUp(): void
    {
        $this->twigMock = $this->createMock(Twig::class);
        $this->validatorFactoryMock = $this->createMock(RequestValidatorFactoryInterface::class);
        $this->authMock = $this->createMock(Auth::class);
        $this->controller = new AuthController($this->twigMock, $this->validatorFactoryMock, $this->authMock);
    }

    public function testLoginViewRendersLoginTemplate()
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $requestMock = $this->createMock(ServerRequestInterface::class);

        $this->twigMock->expects($this->once())
            ->method('render')
            ->with($responseMock, 'auth/login.twig')
            ->willReturn($responseMock);

        $response = $this->controller->loginView($requestMock, $responseMock);

        $this->assertSame($responseMock, $response);
    }

    public function testLoginRedirectsToUserForRegularUserRole()
    {
        // Mock request and response
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);

        // Mocking parsed body for the request
        $requestMock->method('getParsedBody')
            ->willReturn(['email' => 'user@example.com', 'password' => 'correct-password']);

        // Mocking UserLoginRequestValidator behavior
        $validatorMock = $this->createMock(UserLoginRequestValidator::class);
        $validatorMock->method('validate')
            ->willReturn(['email' => 'user@example.com', 'password' => 'correct-password']);

        $this->validatorFactoryMock->method('make')
            ->willReturn($validatorMock);

        // Mocking Auth behavior
        $this->authMock->method('attemptLogin')
            ->with(['email' => 'user@example.com', 'password' => 'correct-password'])
            ->willReturn(true);

        // Mocking Role entity
        $roleMock = $this->createMock(Role::class);
        $roleMock->method('getName')->willReturn('User'); // Ensure 'User' is returned

        // Mocking the authenticated user
        $userMock = $this->createMock(UserInterface::class);
        $userMock->method('getRole')->willReturn($roleMock);

        $this->authMock->method('user')->willReturn($userMock);

        // Expecting response to redirect to '/user'
        $responseMock->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/user') // Expected '/user'
            ->willReturnSelf();

        $responseMock->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        // Call the controller method
        $result = $this->controller->login($requestMock, $responseMock);

        // Assert the response
        $this->assertSame($responseMock, $result);
    }


    public function testLoginRedirectsToAdminForAdminRole()
    {
        // Mock request and response
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);

        // Mocking parsed body for the request
        $requestMock->method('getParsedBody')
            ->willReturn(['email' => 'admin@example.com', 'password' => 'correct-password']);

        // Mocking UserLoginRequestValidator behavior
        $validatorMock = $this->createMock(UserLoginRequestValidator::class);
        $validatorMock->method('validate')
            ->willReturn(['email' => 'admin@example.com', 'password' => 'correct-password']);

        $this->validatorFactoryMock->method('make')
            ->willReturn($validatorMock);

        // Mocking Auth behavior
        $this->authMock->method('attemptLogin')
            ->with(['email' => 'admin@example.com', 'password' => 'correct-password'])
            ->willReturn(true);

        // Mocking the authenticated user
        $roleMock = $this->createMock(Role::class);
        $roleMock->method('getName')->willReturn('Admin');

        $userMock = $this->createMock(UserInterface::class);
        $userMock->method('getRole')->willReturn($roleMock);

        $this->authMock->method('user')->willReturn($userMock);

        // Mocking response behavior for redirection
        $responseMock->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/admin/products')
            ->willReturnSelf();

        $responseMock->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        // Call the controller method
        $result = $this->controller->login($requestMock, $responseMock);

        // Assert the response
        $this->assertSame($responseMock, $result);
    }


    public function testLoginThrowsValidationExceptionOnInvalidCredentials()
    {
        $this->expectException(ValidationException::class);

        // Mocking the request and response
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);

        // Mocking Auth behavior
        $this->authMock->method('attemptLogin')->willReturn(false);

        // Mocking the validator behavior
        $validatorMock = $this->createMock(UserLoginRequestValidator::class);
        $validatorMock->method('validate')
            ->with(['email' => 'invalid@example.com', 'password' => 'wrong-password'])
            ->willReturn(['email' => 'invalid@example.com', 'password' => 'wrong-password']);

        $this->validatorFactoryMock->method('make')->willReturn($validatorMock);

        // Mocking parsed body of the request
        $requestMock->method('getParsedBody')
            ->willReturn(['email' => 'invalid@example.com', 'password' => 'wrong-password']);

        // Calling the controller method
        $this->controller->login($requestMock, $responseMock);
    }


    public function testLogOutRedirectsToHome()
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);

        $responseMock->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/')
            ->willReturnSelf();

        $responseMock->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $this->authMock->expects($this->once())->method('logOut');

        $response = $this->controller->logOut($requestMock, $responseMock);

        $this->assertSame($responseMock, $response);
    }
}
