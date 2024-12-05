<?php

namespace App\Controllers;

use App\Auth;
use App\Contracts\RequestValidatorFactoryInterface;
use App\Enum\RoleEnum;
use App\Exceptions\ValidationException;
use App\Requests\UserLoginRequestValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    public function __construct(private readonly Twig $twig, 
    private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
    private readonly Auth $auth)
    {

    }

    public function loginView(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'auth/login.twig');
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(UserLoginRequestValidator::class)->validate($request->getParsedBody());
    
        if (!$this->auth->attempt($data)) {
            throw new ValidationException(['password' => 'You have entered invalid email or password']);
        }
    
        $user = $this->auth->user();
    
        if ($user->getRole()->getName() === RoleEnum::Admin->value) {
            return $response->withHeader('Location', '/admin')->withStatus(302);
        }
    
        return $response->withHeader('Location', '/user')->withStatus(302);
    }
    

    public function logOut(Request $request,Response $response):Response
    {
        $this->auth->logOut();
        return $response->withHeader('Location','/')->withStatus(302);
    }
}