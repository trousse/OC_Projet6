<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_REF = 'user_';

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    private function getUsers()
    {
        return [
            [
                "id" => 1,
                "name" => "thomas",
                "email" => "ths.rousse@gmail.com",
                "password" => "dev",
                "roles" => ["ROLE_ADMIN"]
            ],
            [
                "id" => 2,
                "name" => "jerome",
                "email" => "jerome@email.fr",
                "password" => "dev",
                "roles" => ["ROLE_ADMIN"]
            ],
            [
                "id" => 3,
                "name" => "ayoub",
                "email" => "ayoub@email.fr",
                "password" => "dev",
            ],
            [
                "id" => 4,
                "name" => "mathilde",
                "email" => "mathilde@email.fr",
                "password" => "dev",
            ],
            [
                "id" => 5,
                "name" => "david",
                "email" => "david@email.fr",
                "password" => "dev",
                "roles" => ["ROLE_ADMIN"]
            ]
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->getUsers();
        foreach ($users as $user){
            $newUser = new User();
            $newUser->setEmail($user['email']);
            $newUser->setUsername($user['name']);
            $newUser->setEnabled(true);

            $hashedPassword = $this->passwordHasher->hashPassword($newUser, $user['password']);

            $newUser->setPassword($hashedPassword);
            if(isset($user['roles'])) $newUser->setRoles($user['roles']);

            $this->addReference(self::USER_REF. $newUser->getUsername(), $newUser);

            $manager->persist($newUser);
        }
        $manager->flush();
    }
}
