<?php

namespace App\Services;

use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\UserInterface;
use App\Contracts\UserProviderServiceInterface;
use App\Entity\User;

class UserProviderService implements UserProviderServiceInterface
{

    public function __construct(private readonly EntityManagerServiceInterface $entityManager){
        
    }


    public function getById(int $userId) : ?UserInterface
    {
        return $this->entityManager->find(User::class,$userId);
    }
    public function getByCredentials(array $credentials) : ?UserInterface
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
    }

   

}
