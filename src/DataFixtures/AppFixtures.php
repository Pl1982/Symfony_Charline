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
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // User admin
        $user = new User();

        $user
            ->setEmail('admin@test.com')
            ->setFirstName('Pierre')
            ->setLastName('Bertrand')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword(
                $this->hasher->hashPassword(new User(), 'Test1234')
            );

        $manager->persist($user);

        for ($i = 1; $i <= 10; ++$i) {
            $user = new User();

            $user
                ->setEmail("user-$i@test.com")
                ->setFirstName("User $i")
                ->setLastName('Test')
                ->setPassword(
                    $this->hasher->hashPassword(new User(), 'Test1234')
                );

            $manager->persist($user);
        }

        $manager->flush();
    }
}
