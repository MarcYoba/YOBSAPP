<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@test.com');
        // Hachage sécurisé du mot de passe
        $user->setPassword($this->hasher->hashPassword($user, 'password123'));

        $manager->persist($user);
        $manager->flush();
    }
}
