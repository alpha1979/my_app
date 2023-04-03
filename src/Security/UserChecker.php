<?php

namespace App\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use DateTime;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserChecker implements UserCheckerInterface
{
    /**
     * @param User $user
     */
	public function checkPreAuth(UserInterface $user): void
    {
        if (null === $user->getBannedUntil()) {
            return;
        }

        $now = new DateTime();

        if ($now < $user->getBannedUntil()) {
            throw new AccessDeniedHttpException('The user is banned');
        }
	}
	
     /**
     * @param User $user
     */
	public function checkPostAuth(UserInterface $user): void
    {
	}
}