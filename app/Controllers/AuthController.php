<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Contracts\AuthInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use App\Enum\RoleEnum;
use App\Exception\ValidationException;
use App\RequestValidators\UserLoginRequestValidator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class AuthController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly AuthInterface $auth    
    ) {
    }


    public function loginView(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'auth/login.twig');
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(UserLoginRequestValidator::class)->validate($request->getParsedBody());
    
        if (!$this->auth->attemptLogin($data)) {
            throw new ValidationException(['password' => 'You have entered invalid email or password']);
        }
    
        $user = $this->auth->user();
    
        if ($user->getRole()->getName() === RoleEnum::Admin->value) {
            return $response->withHeader('Location', '/admin/products')->withStatus(302);
        }
    
        return $response->withHeader('Location', '/user')->withStatus(302);
    }
    

    public function logOut(Request $request,Response $response):Response
    {
        $this->auth->logOut();
        return $response->withHeader('Location','/login')->withStatus(302);
    }
}