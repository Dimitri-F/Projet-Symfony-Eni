<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        // Si l'utilisateur n'est pas vérifié
        if (!$user->isVerified()) {
            // Vous pouvez personnaliser ce message d'erreur
            throw new CustomUserMessageAccountStatusException('Your user account is not verified yet.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        // Vous pouvez ajouter d'autres vérifications après l'authentification ici
    }
}
