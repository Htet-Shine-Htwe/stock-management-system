<?php

namespace Database\Seeders;

use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class DatabaseSeeder
{
    private EntityManagerInterface $entityManager;
    private ConsoleOutput $output;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->output = new ConsoleOutput();
    }

    public function run()
    {
        $this->seedRoles();
        $this->seedUsers();
    }

    private function seedRoles()
    {
        $roles = [
            ['name' => 'Admin'],
            ['name' => 'User']
        ];

        foreach ($roles as $roleData) {
            $role = new Role(); // Replace with your Role entity namespace
            $role->setName($roleData['name']);
            $this->entityManager->persist($role);
        }

        $this->entityManager->flush();

        $this->output->writeln('Roles seeded successfully.');
    }

    private function seedUsers()
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'role' => 'Admin'
            ],
            [
                'name' => 'User 1',
                'email' => 'user1@example.com',
                'password' => password_hash('user123', PASSWORD_BCRYPT),
                'role' => 'User'
            ]
        ];

        foreach ($users as $userData) {
            $role = $this->entityManager
                ->getRepository(\App\Entity\Role::class) // Replace with your Role entity namespace
                ->findOneBy(['name' => $userData['role']]);

            $user = new \App\Entity\User(); // Replace with your User entity namespace
            $user->setName($userData['name']);
            $user->setEmail($userData['email']);
            $user->setPassword($userData['password']);
            $user->setRole($role);
         

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        $this->output->writeln('Users seeded successfully.');
    }
}
