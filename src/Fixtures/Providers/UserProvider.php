<?php

namespace App\Fixtures\Providers;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProvider
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ){
    }

    public function hashUserPassword(string $plainPassword): string  // on définit la méthode dont on se sert dans le fichier UserFixtures.yaml
    {
        return $this->hasher->hashPassword(new User, $plainPassword);
    }
}